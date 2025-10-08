<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsApp\WhatsAppService;

class WhatsAppChatboxController extends Controller
{
    protected WhatsAppService $whatsApp;

    public function __construct(WhatsAppService $whatsApp)
    {
        $this->whatsApp = $whatsApp;
    }
    // public function index(Request $request)
    // {
    //     $this->checkAuthorization(auth()->user(), ['customers.view']);

    //     $q = trim((string) $request->get('q', ''));

    //     $customers = Customer::query()
    //         ->when($q, function ($query) use ($q) {
    //             $query->where('name', 'like', "%{$q}%")
    //                   ->orWhere('whatsapp_number', 'like', "%{$q}%");
    //         })
    //         ->orderBy('name')
    //         ->paginate(30)
    //         ->withQueryString();

    //     $selectedId = $request->integer('customer_id') ?: optional($customers->first())->id;

    //     return view('backend.pages.chatbox.index', compact('customers', 'selectedId', 'q'));
    // }


    public function index(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['chatbox.view']);

        $q = trim((string) $request->get('q', ''));

        $customers = Customer::query()
            ->addSelect([
                'latest_message_timestamp' => WhatsAppMessage::select('timestamp')
                    ->whereColumn('phone_number', 'customers.whatsapp_number')
                    ->orderByDesc('timestamp')
                    ->limit(1),
            ])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('whatsapp_number', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('latest_message_timestamp') // 🔑 order by latest message first
            ->paginate(10)
            ->withQueryString();

        $selectedId = $request->integer('customer_id') ?: optional($customers->first())->id;

        // If it's an infinite scroll request, return only the chunk
        if ($request->ajax()) {
            return view('backend.pages.chatbox._customers', [
                'customers' => $customers,
                'selectedId' => $selectedId,
            ]);
        }

        return view('backend.pages.chatbox.index', compact('customers', 'selectedId', 'q'));
    }

    public function chat(Request $request, int $customerId)
    {
        // Reuse the same data logic as CustomersController
        $customer = Customer::with([
            'whatsappConversation.messages' => fn($q) => $q->orderBy('timestamp', 'asc')
        ])->findOrFail($customerId);

        $messages = optional($customer->whatsappConversation)->messages ?? collect();

        // If AJAX, return only the partial (for right pane)
        if ($request->ajax()) {
            return view('backend.pages.chatbox._chat', [
                'customer' => $customer,
                'messages' => $messages,
            ]);
        }

        // Fallback: full page (same as CustomersController)
        return view('backend.pages.customers.chat', [
            'customer' => $customer,
            'messages' => $messages,
        ]);
    }

    public function downloadMedia(string $mediaId)
    {
        $media = $this->whatsApp->downloadMedia($mediaId);

        if (!$media) {
            return response()->json(['error' => 'Unable to fetch media'], 500);
        }

        return response($media['content'], 200)
            ->header('Content-Type', $media['mime_type']);
    }
}
