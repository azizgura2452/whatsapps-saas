<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\SendScheduledBroadcastJob;
use App\Models\Broadcast;
use App\Models\BroadcastGroup;
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
        $broadcasts = Broadcast::with('broadcastGroup')
            ->where('business_id', app('current_business')->id)
            ->latest()
            ->paginate(config('settings.default_pagination', 10));
            
        return view('backend.pages.broadcasts.index', compact('broadcasts'));
    }

    public function create()
    {
        $templates = $this->whatsAppService->getTemplates();
        $broadcastGroups = BroadcastGroup::where('business_id', app('current_business')->id)->get();
        return view('backend.pages.broadcasts.create', compact('templates', 'broadcastGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'whatsapp_template_name' => 'required|string',
            'custom_template' => 'required|string',
            'custom_recipients' => 'nullable|string',
            'broadcast_group_id' => 'nullable|exists:broadcast_groups,id',
            'recipient_source' => 'required|in:all,custom,group',
            'send_type' => 'required|in:immediate,scheduled',
            'scheduled_at' => 'required_if:send_type,scheduled|nullable|date|after:now',
        ]);

        // Determine status
        $status = $request->send_type === 'scheduled' ? 'scheduled' : 'draft';
        $scheduledAt = $request->send_type === 'scheduled' 
            ? \Carbon\Carbon::parse($request->scheduled_at, config('app.timezone'))->setTimezone('UTC')
            : null;

        $broadcast = Broadcast::create([
            'business_id' => app('current_business')->id,
            'whatsapp_template_name' => $request->whatsapp_template_name,
            'broadcast_title' => $request->broadcast_title,
            'custom_template' => $request->custom_template,
            'custom_recipients' => $request->custom_recipients,
            'broadcast_group_id' => $request->broadcast_group_id,
            'status' => $status,
            'scheduled_at' => $scheduledAt,
        ]);

        // If immediate send, dispatch job now
        if ($request->send_type === 'immediate') {
            SendScheduledBroadcastJob::dispatch($broadcast);
            
            return redirect()->route('admin.broadcasts.index')
                ->with('success', __('Broadcast is being sent to recipients.'));
        }

        // If scheduled
        $scheduledTime = \Carbon\Carbon::parse($scheduledAt, 'UTC')
            ->setTimezone(config('app.timezone'))
            ->format('M d, Y h:i A');
            
        return redirect()->route('admin.broadcasts.index')
            ->with('success', __('Broadcast scheduled for :time', ['time' => $scheduledTime]));
    }

    public function report(int $id)
    {
        $broadcast = Broadcast::with(['messages.conversation.customer', 'broadcastGroup'])
            ->where('business_id', app('current_business')->id)
            ->findOrFail($id);

        $messages = $broadcast->messages()
            ->with('conversation.customer')
            ->get();

        $stats = [
            'total' => $messages->count(),
            'sent' => $messages->where('status', 'sent')->count(),
            'delivered' => $messages->where('status', 'delivered')->count(),
            'read' => $messages->where('status', 'read')->count(),
            'failed' => $messages->where('status', 'failed')->count(),
            'success_rate' => $broadcast->getSuccessRate(),
        ];

        return view('backend.pages.broadcasts.report', compact('broadcast', 'messages', 'stats'));
    }

    public function destroy(int $id)
    {
        $broadcast = Broadcast::where('business_id', app('current_business')->id)->findOrFail($id);

        // Only allow deletion if not sent or sending
        if (in_array($broadcast->status, ['sending', 'sent'])) {
            return back()->with('error', __('Cannot delete a broadcast that has been sent or is currently sending.'));
        }

        $broadcast->delete();

        return back()->with('success', __('Broadcast deleted successfully.'));
    }
}