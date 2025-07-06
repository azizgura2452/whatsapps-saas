@extends('backend.layouts.app')

@section('title')
    {{ __('Dashboard') }} | {{ config('app.name') }}
@endsection

@section('before_vite_build')
    <script>
        var userGrowthData = @json($user_growth_data['data']);
        var userGrowthLabels = @json($user_growth_data['labels']);
    </script>
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '{{ __('Dashboard') }}' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Dashboard') }}</h2>
            </div>
        </div>

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
    </div>
@endsection
