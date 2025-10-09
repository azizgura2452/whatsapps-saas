@extends('backend.layouts.app')

@section('title')
    {{ __('Edit Flow Step') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-4xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Edit Flow Step') }}: {{ $step->name }}
        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.flow-builder.index') }}">
                        {{ __('Flow Builder') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Edit') }}</li>
            </ol>
        </nav>
    </div>

    @include('backend.layouts.partials.messages')

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('admin.flow-builder.update', $step->id) }}" 
              method="POST" 
              x-data="flowStepForm()">
            @csrf
            @method('PUT')

            <!-- Rest of the form is exactly the same as create.blade.php -->
            <!-- Just change old() to old('field', $step->field) -->
            
            <!-- Basic Information -->
            <div class="space-y-4 mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Basic Information') }}</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                        {{ __('Step Name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                           value="{{ old('name', $step->name) }}"
                           placeholder="e.g., Welcome Message"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                        {{ __('Step Type') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="step_type" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                        @foreach($stepTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('step_type', $step->step_type) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                        {{ __('Order') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="order" required min="0"
                           value="{{ old('order', $step->order) }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $step->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-400">
                        {{ __('Active') }}
                    </label>
                </div>
            </div>

            <!-- Messages and Triggers sections - same as create.blade.php -->
            <!-- But with pre-populated data from $step -->

            <!-- Form Actions -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="bi bi-check-circle mr-1"></i>
                    {{ __('Update Step') }}
                </button>
                <a href="{{ route('admin.flow-builder.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function flowStepForm() {
    return {
        messages: @json($step->messages->map(function($msg) {
            return [
                'language' => $msg->language,
                'message_type' => $msg->message_type,
                'message_content' => $msg->message_content,
                'buttons' => is_array($msg->buttons) ? json_encode($msg->buttons, JSON_PRETTY_PRINT) : $msg->buttons,
                'list_sections' => is_array($msg->list_sections) ? json_encode($msg->list_sections, JSON_PRETTY_PRINT) : $msg->list_sections,
                'template_name' => $msg->template_name,
            ];
        })->toArray()),
        
        triggers: @json($step->triggers->map(function($trigger) {
            return [
                'trigger_type' => $trigger->trigger_type,
                'trigger_value' => $trigger->trigger_value,
                'next_step_id' => $trigger->next_step_id,
            ];
        })->toArray()),

        addMessage() {
            this.messages.push({
                language: 'english',
                message_type: 'text',
                message_content: '',
                buttons: '',
                list_sections: '',
                template_name: ''
            });
        },

        removeMessage(index) {
            if (this.messages.length > 1) {
                this.messages.splice(index, 1);
            } else {
                alert('You must have at least one message');
            }
        },

        addTrigger() {
            this.triggers.push({
                trigger_type: 'text',
                trigger_value: '',
                next_step_id: ''
            });
        },

        removeTrigger(index) {
            this.triggers.splice(index, 1);
        }
    }
}
</script>
@endpush
@endsection