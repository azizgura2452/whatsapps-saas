<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Services\WhatsApp\ConversationStateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use MyFatoorah\Library\MyFatoorah;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentEmbedded;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use App\Services\WhatsApp\WhatsAppService;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use Exception;

class MyFatoorahController extends Controller
{

    /**
     * @var array
     */
    public $mfConfig = [];
    protected $whatsAppService;
    protected $stateService;
    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Initiate MyFatoorah Configuration
     */
    public function __construct(WhatsAppService $whatsAppService, ConversationStateService $stateService)
    {
        $this->mfConfig = [
            'apiKey' => config('services.myfatoorah.api_key'),
            'isTest' => config('services.myfatoorah.test_mode'),
            'countryCode' => config('services.myfatoorah.country_iso'),
        ];
        $this->whatsAppService = $whatsAppService;
        $this->stateService = $stateService;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Redirect to MyFatoorah Invoice URL
     * Provide the index method with the order id and (payment method id or session id)
     *
     * @return Response
     */
    public function index()
    {
        try {
            //For example: pmid=0 for MyFatoorah invoice or pmid=1 for Knet in test mode
            $paymentId = request('pmid') ?: 0;
            $sessionId = request('sid') ?: null;

            $orderId = request('oid') ?: 147;
            $curlData = $this->getPayLoadData($orderId);

            $mfObj = new MyFatoorahPayment($this->mfConfig);
            $payment = $mfObj->getInvoiceURL($curlData, $paymentId, $orderId, $sessionId);

            return redirect($payment['invoiceURL']);
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return response()->json(['IsSuccess' => 'false', 'Message' => $exMessage]);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Example on how to map order data to MyFatoorah
     * You can get the data using the order object in your system
     * 
     * @param int|string $orderId
     * 
     * @return array
     */
    private function getPayLoadData($orderId = null)
    {
        $callbackURL = route('myfatoorah.callback');
        $returnURL = route('myfatoorah.success', ['order_id' => $orderId]);

        //You can get the data using the order object in your system
        $order = $this->getTestOrderData($orderId);

        return [
            'CustomerName' => 'FName LName',
            'InvoiceValue' => $order['total'],
            'DisplayCurrencyIso' => $order['currency'],
            'CustomerEmail' => 'test@test.com',
            'CallBackUrl' => $callbackURL,
            'ReturnUrl' => $returnURL,
            'ErrorUrl' => $callbackURL,
            'MobileCountryCode' => '+965',
            'CustomerMobile' => '12345678',
            'Language' => 'en',
            'CustomerReference' => $orderId,
            'SourceInfo' => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package'
        ];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get MyFatoorah Payment Information
     * Provide the callback method with the paymentId
     * 
     * @return Response
     */
    public function callback(Request $request)
    {
        $response = ['IsSuccess' => false, 'Message' => 'Unknown error'];

        try {
            $paymentId = $request->input('paymentId');

            $mfObj = new MyFatoorahPaymentStatus($this->mfConfig);
            $data = $mfObj->getPaymentStatus($paymentId, 'PaymentId');

            if ($data->InvoiceStatus === 'Paid') {
                // Retrieve order by CustomerReference (which should be order ID)
                $orderId = $data->CustomerReference;
                $order = Order::find($orderId);

                if ($order) {
                    $order->status = OrderStatus::Paid;
                    $order->save();

                    // Deduct stock
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->stock -= $item->quantity;
                            $product->save();
                        }
                    }

                    // Create invoice record
                    $invoice = Invoice::updateOrCreate(
                        ['order_id' => $order->id],  // Unique by order_id or create new
                        [
                            'invoice_status' => $data->InvoiceStatus,
                            'invoice_reference' => $data->InvoiceReference,
                            'customer_reference' => $data->CustomerReference,
                            'created_date' => $data->CreatedDate,
                            'expiry_date' => $data->ExpiryDate,
                            'expiry_time' => $data->ExpiryTime,
                            'invoice_value' => $data->InvoiceValue,
                            'comments' => $data->Comments,
                            'customer_name' => $data->CustomerName,
                            'customer_mobile' => $data->CustomerMobile,
                            'customer_email' => $data->CustomerEmail,
                            'user_defined_field' => $data->UserDefinedField,
                            'invoice_display_value' => $data->InvoiceDisplayValue,
                            'due_deposit' => $data->DueDeposit,
                            'deposit_status' => $data->DepositStatus,
                        ]
                    );

                    // Compose message for customer
                    $message = "Dear {$data->CustomerName}, your payment for Order #{$order->id} amounting to {$data->InvoiceDisplayValue} has been received successfully. Thank you for your purchase.";

                    // Format customer phone number properly (remove leading + if needed)
                    $customerMobile = $data->CustomerMobile; // e.g. +96512345678
                    $to = preg_replace('/^\+/', '', $customerMobile); // remove plus sign for WhatsApp API
                    // Send WhatsApp message
                    $this->stateService->resetState($to);
                    $this->whatsAppService->sendText($to, $message);
                }
            }

            Log::info("Payment Response", (array) $data);

            $response = ['IsSuccess' => true, 'Message' => 'Payment processed successfully', 'Data' => $data];
        } catch (Exception $ex) {
            Log::error('Payment callback error: ' . $ex->getMessage());
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            $response = ['IsSuccess' => false, 'Message' => $exMessage];
        }

        return redirect()->route('myfatoorah.success', ['order_id' => $orderId ?? null]);
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->input('order_id');  // you may pass this in URL or session
        // Optionally, verify order/payment status here again

        return view('success', ['orderId' => $orderId]);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Example on how to Display the enabled gateways at your MyFatoorah account to be displayed on the checkout page
     * Provide the checkout method with the order id to display its total amount and currency
     * 
     * @return View
     */
    public function checkout()
    {
        try {
            //You can get the data using the order object in your system
            $orderId = request('oid') ?: 147;
            $order = $this->getTestOrderData($orderId);

            //You can replace this variable with customer Id in your system
            $customerId = request('customerId');

            //You can use the user defined field if you want to save card
            $userDefinedField = config('myfatoorah.save_card') && $customerId ? "CK-$customerId" : '';

            //Get the enabled gateways at your MyFatoorah acount to be displayed on checkout page
            $mfObj = new MyFatoorahPaymentEmbedded($this->mfConfig);
            $paymentMethods = $mfObj->getCheckoutGateways($order['total'], $order['currency'], config('myfatoorah.register_apple_pay'));

            if (empty($paymentMethods['all'])) {
                throw new Exception('noPaymentGateways');
            }

            //Generate MyFatoorah session for embedded payment
            $mfSession = $mfObj->getEmbeddedSession($userDefinedField);

            //Get Environment url
            $isTest = $this->mfConfig['isTest'];
            $vcCode = $this->mfConfig['countryCode'];

            $countries = MyFatoorah::getMFCountries();
            $jsDomain = ($isTest) ? $countries[$vcCode]['testPortal'] : $countries[$vcCode]['portal'];

            return view('myfatoorah.checkout', compact('mfSession', 'paymentMethods', 'jsDomain', 'userDefinedField'));
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return view('myfatoorah.error', compact('exMessage'));
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Example on how the webhook is working when MyFatoorah try to notify your system about any transaction status update
     */
    public function webhook(Request $request)
    {
        try {
            //Validate webhook_secret_key
            $secretKey = config('myfatoorah.webhook_secret_key');
            if (empty($secretKey)) {
                return response(null, 404);
            }

            //Validate MyFatoorah-Signature
            $mfSignature = $request->header('MyFatoorah-Signature');
            if (empty($mfSignature)) {
                return response(null, 404);
            }

            //Validate input
            $body = $request->getContent();
            $input = json_decode($body, true);
            if (empty($input['Data']) || empty($input['EventType']) || $input['EventType'] != 1) {
                return response(null, 404);
            }

            //Validate Signature
            if (!MyFatoorah::isSignatureValid($input['Data'], $secretKey, $mfSignature, $input['EventType'])) {
                return response(null, 404);
            }

            //Update Transaction status on your system
            $result = $this->changeTransactionStatus($input['Data']);

            return response()->json($result);
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return response()->json(['IsSuccess' => false, 'Message' => $exMessage]);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
    private function changeTransactionStatus($inputData)
    {
        //1. Check if orderId is valid on your system.
        $orderId = $inputData['CustomerReference'];

        //2. Get MyFatoorah invoice id
        $invoiceId = $inputData['InvoiceId'];

        //3. Check order status at MyFatoorah side
        if ($inputData['TransactionStatus'] == 'SUCCESS') {
            $status = 'Paid';
            $error = '';
        } else {
            $mfObj = new MyFatoorahPaymentStatus($this->mfConfig);
            $data = $mfObj->getPaymentStatus($invoiceId, 'InvoiceId');

            $status = $data->InvoiceStatus;
            $error = $data->InvoiceError;
        }

        $message = $this->getTestMessage($status, $error);

        //4. Update order transaction status on your system
        return ['IsSuccess' => true, 'Message' => $message, 'Data' => $inputData];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
    private function getTestOrderData($orderId)
    {
        return [
            'total' => 15,
            'currency' => 'KWD'
        ];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
    private function getTestMessage($status, $error)
    {
        if ($status == 'Paid') {
            return 'Invoice is paid.';
        } else if ($status == 'Failed') {
            return 'Invoice is not paid due to ' . $error;
        } else if ($status == 'Expired') {
            return $error;
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
}
