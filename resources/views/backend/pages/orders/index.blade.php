@extends('backend.layouts.app')

@section('title')
    {{ __('Orders') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div x-data="{ pageName: '{{ __('Orders') }}' }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                {{ __('Orders') }}
            </h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                            {{ __('Home') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Orders') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Order List') }}</h3>

                @include('backend.partials.search-form', [
                    'placeholder' => __('Search by Order ID (only digits)'),
                ])

                @if (auth()->user()->can('orders.create'))
                    <!-- <a href="{{ route('admin.orders.create') }}" class="btn-primary">
                        <i class="bi bi-plus-circle mr-2"></i>
                        {{ __('New Order') }}
                    </a> -->
                @endif
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                @include('backend.layouts.partials.messages')
                <table id="dataTable" class="w-full dark:text-gray-400">
                    <thead class="bg-light text-capitalize">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('#') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Order ID') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Customer Name') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Total') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Delivery Charge') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Source') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Status') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Created On') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Modified On') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6">{{ $orders->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $order->id }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $order->customer->name }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ number_format($order->total, 3) }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ number_format($order->delivery_charge, 3) }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ ucfirst($order->source) }}</td>
                                <td class="px-5 py-4 sm:px-6">
    @php
        // Get the string value of the enum
        $statusValue = strtolower($order->status->value);

        $statusClasses = [
            'pending' => 'bg-blue-100 text-blue-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-green-100 text-green-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        $statusText = ucfirst($statusValue);
        $statusClass = $statusClasses[$statusValue] ?? 'bg-gray-100 text-gray-800';
    @endphp

    <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
        {{ $statusText }}
    </span>
</td>


                                <td class="px-5 py-4 sm:px-6">{{ \Carbon\Carbon::parse($order->created_on)->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ \Carbon\Carbon::parse($order->modified_on)->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-4 sm:px-6 flex gap-2">
                                    @if (auth()->user()->can('orders.view'))
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-default">
                                            <i class="bi bi-eye"></i>
                                            <span>{{ __('View') }}</span>
                                        </a>
                                    @endif
                                    @if (auth()->user()->can('orders.edit'))
                                        <!-- <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn-default">
                                            <i class="bi bi-pencil"></i>
                                            <span>{{ __('Edit') }}</span>
                                        </a> -->
                                    @endif
                                    @if (auth()->user()->can('orders.delete'))
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger">
                                                <i class="bi bi-trash"></i>
                                                <span>{{ __('Delete') }}</span>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('No orders found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection