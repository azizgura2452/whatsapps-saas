@extends('backend.layouts.app')

@section('title')
    {{ __('Flow Builder') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-7xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Flow Builder') }}</h2>
            @if($business)
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ __('Managing flow for') }}: <strong>{{ $business->name }}</strong>
            </p>
            @endif
        </div>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Flow Builder') }}</li>
            </ol>
        </nav>
    </div>

    @include('backend.layouts.partials.messages')

    <!-- React Flow Builder Component -->
    <div id="flow-builder-root"></div>
</div>

@push('scripts')
<script>
    // Pass flow steps data to React component
    window.flowStepsData = {!! json_encode($flowSteps->map(function($step) {
        return [
            'id' => $step->id,
            'name' => $step->name,
            'step_type' => $step->step_type,
            'order' => $step->order,
            'is_active' => $step->is_active,
            'messages' => $step->messages,
            'triggers' => $step->triggers,
        ];
    })->values()) !!};
</script>
@endpush
@endsection