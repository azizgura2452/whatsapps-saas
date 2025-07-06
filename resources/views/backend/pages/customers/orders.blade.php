@extends('backend.layouts.app')

@section('title')
    {{ __('Orders of') }} {{ $customer->name }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div x-data="{ pageName: '{{ __('Orders of') }} {{ $customer->name }}' }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                {{ __('Orders of') }} {{ $customer->name }}
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
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.customers.index') }}">
                            {{ __('Customers') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Orders') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Customer Orders Table -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ __('Orders for') }} {{ $customer->name }}
                </h3>
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                @include('backend.layouts.partials.messages')
                <table class="w-full dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left">{{ __('#') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Order ID') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Total') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Delivery Charge') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Source') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Created On') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Modified On') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4">{{ $order->id }}</td>
                                <td class="px-5 py-4">{{ number_format($order->total, 3) }}</td>
                                <td class="px-5 py-4">{{ number_format($order->delivery_charge, 3) }}</td>
                                <td class="px-5 py-4">{{ ucfirst($order->source) }}</td>
                                <td class="px-5 py-4">
                                    @php
                                        $statusValue = strtolower($order->status->value ?? 'pending');
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
                                <td class="px-5 py-4">{{ \Carbon\Carbon::parse($order->created_on)->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-4">{{ \Carbon\Carbon::parse($order->modified_on)->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-4 flex gap-2">
                                    @if (auth()->user()->can('orders.view'))
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-default">
                                            <i class="bi bi-eye"></i>
                                            <span>{{ __('View') }}</span>
                                        </a>
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
                                <td colspan="9" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    {{ __('No orders found for this customer.') }}
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
