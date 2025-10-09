<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ChatFlowStep;
use App\Models\ChatFlowMessage;
use App\Models\ChatFlowTrigger;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlowBuilderController extends Controller
{
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['flow.view']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please select a business first.'));
            return redirect()->route('admin.businesses.index');
        }

        $flowSteps = $business->flowSteps()
            ->orderBy('order')
            ->with(['messages', 'triggers'])
            ->get();

        return view('backend.pages.flow-builder.index', compact('business', 'flowSteps'));
    }

    public function create()
    {
        $this->checkAuthorization(auth()->user(), ['flow.create']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please select a business first.'));
            return redirect()->route('admin.businesses.index');
        }

        $stepTypes = $this->getStepTypes();

        return view('backend.pages.flow-builder.create', compact('business', 'stepTypes'));
    }

    public function store(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['flow.create']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please select a business first.'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'step_type' => 'required|string',
            'order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'config' => 'nullable|array',
            
            // Messages
            'messages' => 'required|array|min:1',
            'messages.*.language' => 'required|string',
            'messages.*.message_type' => 'required|string',
            'messages.*.message_content' => 'required|string',
            'messages.*.buttons' => 'nullable|string',
            'messages.*.list_sections' => 'nullable|string',
            'messages.*.template_name' => 'nullable|string',
            
            // Triggers
            'triggers' => 'nullable|array',
            'triggers.*.trigger_type' => 'required|string',
            'triggers.*.trigger_value' => 'required|string',
            'triggers.*.next_step_id' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $step = ChatFlowStep::create([
                'business_id' => $business->id,
                'name' => $validated['name'],
                'step_type' => $validated['step_type'],
                'order' => $validated['order'],
                'is_active' => $request->has('is_active') ? true : false,
                'config' => $validated['config'] ?? null,
            ]);

            // Create messages
            foreach ($validated['messages'] as $messageData) {
                // Parse JSON strings for buttons and list_sections
                $buttons = null;
                if (!empty($messageData['buttons'])) {
                    $buttons = json_decode($messageData['buttons'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON format for buttons');
                    }
                }

                $listSections = null;
                if (!empty($messageData['list_sections'])) {
                    $listSections = json_decode($messageData['list_sections'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON format for list sections');
                    }
                }

                ChatFlowMessage::create([
                    'chat_flow_step_id' => $step->id,
                    'language' => $messageData['language'],
                    'message_type' => $messageData['message_type'],
                    'message_content' => $messageData['message_content'],
                    'buttons' => $buttons,
                    'list_sections' => $listSections,
                    'template_name' => $messageData['template_name'] ?? null,
                ]);
            }

            // Create triggers
            if (!empty($validated['triggers'])) {
                foreach ($validated['triggers'] as $triggerData) {
                    ChatFlowTrigger::create([
                        'chat_flow_step_id' => $step->id,
                        'trigger_type' => $triggerData['trigger_type'],
                        'trigger_value' => $triggerData['trigger_value'],
                        'next_step_id' => $triggerData['next_step_id'] ?? null,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', __('Flow step has been created successfully.'));
            return redirect()->route('admin.flow-builder.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create flow step: ' . $e->getMessage());
            return back()->withInput()->with('error', __('Failed to create flow step: ') . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $this->checkAuthorization(auth()->user(), ['flow.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please select a business first.'));
            return redirect()->route('admin.businesses.index');
        }

        $step = ChatFlowStep::with(['messages', 'triggers'])
            ->where('business_id', $business->id)
            ->findOrFail($id);

        $stepTypes = $this->getStepTypes();
        $allSteps = $business->flowSteps()
            ->where('id', '!=', $step->id)
            ->orderBy('order')
            ->get();

        return view('backend.pages.flow-builder.edit', compact('business', 'step', 'stepTypes', 'allSteps'));
    }

    public function update(Request $request, int $id)
    {
        $this->checkAuthorization(auth()->user(), ['flow.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please select a business first.'));
        }

        $step = ChatFlowStep::where('business_id', $business->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'step_type' => 'required|string',
            'order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'config' => 'nullable|array',
            
            'messages' => 'required|array|min:1',
            'messages.*.language' => 'required|string',
            'messages.*.message_type' => 'required|string',
            'messages.*.message_content' => 'required|string',
            'messages.*.buttons' => 'nullable|string',
            'messages.*.list_sections' => 'nullable|string',
            'messages.*.template_name' => 'nullable|string',
            
            'triggers' => 'nullable|array',
            'triggers.*.trigger_type' => 'required|string',
            'triggers.*.trigger_value' => 'required|string',
            'triggers.*.next_step_id' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $step->update([
                'name' => $validated['name'],
                'step_type' => $validated['step_type'],
                'order' => $validated['order'],
                'is_active' => $request->has('is_active') ? true : false,
                'config' => $validated['config'] ?? null,
            ]);

            // Delete old messages and create new ones
            $step->messages()->delete();
            foreach ($validated['messages'] as $messageData) {
                // Parse JSON strings for buttons and list_sections
                $buttons = null;
                if (!empty($messageData['buttons'])) {
                    $buttons = json_decode($messageData['buttons'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON format for buttons');
                    }
                }

                $listSections = null;
                if (!empty($messageData['list_sections'])) {
                    $listSections = json_decode($messageData['list_sections'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON format for list sections');
                    }
                }

                ChatFlowMessage::create([
                    'chat_flow_step_id' => $step->id,
                    'language' => $messageData['language'],
                    'message_type' => $messageData['message_type'],
                    'message_content' => $messageData['message_content'],
                    'buttons' => $buttons,
                    'list_sections' => $listSections,
                    'template_name' => $messageData['template_name'] ?? null,
                ]);
            }

            // Delete old triggers and create new ones
            $step->triggers()->delete();
            if (!empty($validated['triggers'])) {
                foreach ($validated['triggers'] as $triggerData) {
                    ChatFlowTrigger::create([
                        'chat_flow_step_id' => $step->id,
                        'trigger_type' => $triggerData['trigger_type'],
                        'trigger_value' => $triggerData['trigger_value'],
                        'next_step_id' => $triggerData['next_step_id'] ?? null,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', __('Flow step has been updated successfully.'));
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update flow step: ' . $e->getMessage());
            return back()->withInput()->with('error', __('Failed to update flow step: ') . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        $this->checkAuthorization(auth()->user(), ['flow.delete']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please select a business first.'));
        }

        $step = ChatFlowStep::where('business_id', $business->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            $step->delete();
            DB::commit();

            session()->flash('success', __('Flow step has been deleted successfully.'));
            return redirect()->route('admin.flow-builder.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete flow step: ' . $e->getMessage());
            return back()->with('error', __('Failed to delete flow step: ') . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['flow.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return response()->json(['error' => 'Business not found'], 404);
        }

        $validated = $request->validate([
            'steps' => 'required|array',
            'steps.*.id' => 'required|integer',
            'steps.*.order' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['steps'] as $stepData) {
                ChatFlowStep::where('business_id', $business->id)
                    ->where('id', $stepData['id'])
                    ->update(['order' => $stepData['order']]);
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reorder steps: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to reorder steps'], 500);
        }
    }

    protected function getCurrentBusiness()
    {
        if (app()->has('current_business')) {
            return app('current_business');
        }

        return auth()->user()->businesses()->first();
    }

    protected function getStepTypes()
    {
        return [
            'welcome' => __('Welcome Message'),
            'language_selection' => __('Language Selection'),
            'menu' => __('Main Menu'),
            'catalog' => __('Show Catalog'),
            'support' => __('Customer Support'),
            'collect_name' => __('Collect Name'),
            'collect_address' => __('Collect Address'),
            'payment_confirmation' => __('Payment Confirmation'),
            'order_processing' => __('Order Processing'),
            'custom' => __('Custom Step'),
        ];
    }
}