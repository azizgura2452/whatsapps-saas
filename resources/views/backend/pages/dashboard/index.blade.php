@extends('backend.layouts.app')

@section('title')
    {{ __('Dashboard') }} | {{ config('app.name') }}
@endsection

@section('before_vite_build')
    @if(isset($user_growth_data['data']) && isset($user_growth_data['labels']))
        <script>
            var userGrowthData = @json($user_growth_data['data']);
            var userGrowthLabels = @json($user_growth_data['labels']);
        </script>
    @else
        <script>
            var userGrowthData = [];
            var userGrowthLabels = [];
        </script>
    @endif
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '{{ __('Dashboard') }}' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Dashboard') }}</h2>
            </div>
        </div>

        @php
            $business = app()->has('current_business') ? app('current_business') : auth()->user()->businesses()->first();
        @endphp

        @if($business)
            <div class="grid grid-cols-12 gap-4 md:gap-6">
                <div class="col-span-12 space-y-6">
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-4 md:gap-6">
                        @include('backend.pages.dashboard.partials.card', [
                            'icon' => 'bi bi-card-checklist',
                            'label' => __('Products'),
                            'value' => $total_products,
                            'bg' => '#635BFF',
                            'class' => 'bg-white',
                            'url' => route('admin.products.index'),
                        ])
                        @include('backend.pages.dashboard.partials.card', [
                            'icon_svg' => asset('images/icons/user.svg'),
                            'label' => __('Customers'),
                            'value' => $total_customers,
                            'bg' => '#00D7FF',
                            'class' => 'bg-white',
                            'url' => route('admin.customers.index'),
                        ])
                        @include('backend.pages.dashboard.partials.card', [
                            'icon' => 'bi bi-cash-coin',
                            'label' => __('Orders'),
                            'value' => $total_orders,
                            'bg' => '#FF4D96',
                            'class' => 'bg-white',
                            'url' => route('admin.orders.index'),
                        ])
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <h3 class="text-2xl font-semibold text-gray-700 dark:text-white/80">
                    {{ __('No business associated.') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    {{ __('Please create or select a business to view your dashboard data.') }}
                </p>
                <a href="{{ route('admin.businesses.index') }}"
                   class="inline-block mt-4 px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    {{ __('Go to Businesses') }}
                </a>
            </div>
        @endif
    </div>
@endsection
