<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Charts\UserChartService;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __construct(
        private readonly UserChartService $userChartService,
        private readonly LanguageService $languageService
    ) {
    }

    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['dashboard.view']);

        return view(
            'backend.pages.dashboard.index',
            [
                'total_products' => number_format(Product::count()),
                'total_customers' => number_format(Customer::count()),
                'total_orders' => number_format(Order::count()),
                'languages' => [
                    'total' => number_format(count($this->languageService->getLanguages())),
                    'active' => number_format(count($this->languageService->getActiveLanguages())),
                ],
                'user_growth_data' => $this->userChartService->getUserGrowthData(
                    request()->get('chart_filter_period', 'last_12_months')
                )->getData(true),
                'user_history_data' => $this->userChartService->getUserHistoryData(),
            ]
        );
    }

    public function stats(): JsonResponse
    {
        $this->checkAuthorization(auth()->user(), ['dashboard.view']);

        $stats = [
            'products' => Product::count(),
            'customers' => Customer::count(),
            'orders' => Order::count(),
            'languages' => [
                'total' => count($this->languageService->getLanguages()),
                'active' => count($this->languageService->getActiveLanguages()),
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    /**
     * Send a test email.
     */
    public function sendTestEmail()
    {
        $this->checkAuthorization(auth()->user(), ['dashboard.view']);

        Mail::raw('Hello, this is a test email sent from the DashboardController!', function ($message) {
            $message->to('difora5626@fenexy.com')
                    ->subject('New order received')
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Test email sent successfully!',
        ]);
    }
}
