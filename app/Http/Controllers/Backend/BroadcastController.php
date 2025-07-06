<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BroadcastController extends Controller
{
    public function __construct(
        private readonly WhatsAppService $whatsAppService
    ) {
    }
    public function index()
    {
        $broadcasts = Broadcast::latest()->paginate(20);
        return view('backend.pages.broadcasts.index', compact('broadcasts'));
    }

    public function create()
    {
        // $templates = WhatsAppTemplate::all();
        $templates = $this->whatsAppService->getTemplates();
        return view('backend.pages.broadcasts.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'whatsapp_template_name' => 'required|string',
            'custom_template' => 'required|string',
            'custom_recipients' => 'nullable|string',
        ]);

        $broadcast = Broadcast::create($request->only([
            'whatsapp_template_name',
            'custom_template',
            'custom_recipients'
        ]));

        // Parse custom_template using ~ separator
        // $parts = explode('~', $request->custom_template);

        // if (count($parts) < 3 || in_array('', $parts, true)) {
        //     Log::error("Missing marketing template parameters for broadcast ID {$broadcast->id}");
        //     return redirect()->back()->withErrors(['custom_template' => 'All template parameters must be filled.']);
        // }


        // $params = [
        //     'offer_title' => $parts[0] ?? '',
        //     'offer_description' => $parts[1] ?? '',
        //     'coupon' => $parts[2] ?? ''
        // ];

        // Determine recipients
        // $recipients = $request->filled('custom_recipients')
        //     ? array_map('trim', explode(',', $request->custom_recipients))
        //     : $this->whatsAppService->getAllCustomerNumbers();

        // Send message to each recipient
        // foreach ($recipients as $number) {
        //     try {
        //         $this->whatsAppService->sendMarketingTemplate(
        //             $number,
        //             $params,
        //             $request->whatsapp_template_id,
        //             'en'
        //         );
        //         sleep(3); // Wait 5 seconds before sending the next message
        //     } catch (\Throwable $e) {
        //         Log::error("Failed to send marketing template to {$number}: {$e->getMessage()}");
        //     }
        // }

        return redirect()->route('admin.broadcasts.index')
            ->with('success', 'Broadcast created and messages sent successfully.');
    }
}