@php use App\Enums\OrderStatus; @endphp

@extends('backend.layouts.app')

@section('title')
    {{ __('Order Details') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                {{ __('Order Details') }}
            </h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                            href="{{ route('admin.dashboard') }}">
                            {{ __('Home') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                            href="{{ route('admin.orders.index') }}">
                            {{ __('Orders') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Order #') . $order->id }}</li>
                </ol>
            </nav>
        </div>

        <!-- Order Summary -->
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
            @if(session('success'))
                <div class="mb-4 px-4 py-2 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @error('status')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-4">Order ID: {{ $order->id }}</h3>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300">
                <li><strong>{{ __('Customer Name') }}:</strong> {{ $order->customer->name }}</li>

                <li><strong>{{ __('Order Source') }}:</strong> {{ ucfirst($order->source) }}</li>
                <li><strong>{{ __('Customer Address') }}:</strong> {{ $order->customer->address }}</li>
                <li><strong>{{ __('Customer WhatsApp') }}:</strong> {{ $order->customer->whatsapp_number }}</li>


                <li><strong>{{ __('Date Created') }}:</strong>
                    {{ \Carbon\Carbon::parse($order->created_on)->format('M d, Y h:i A') }}</li>
                <li>
                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST"
                        class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')

                        <label for="status" class="text-gray-700 dark:text-gray-300">
                            <strong>{{ __('Order Status') }}:</strong>
                        </label>
                        <select name="status" id="status"
                            class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm"
                            style="width: 120px">
                            @foreach(OrderStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($order->status->value === $status->value)>
                                    {{ __($status->value) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{ __('Update') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- Order Items Table -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Ordered Items') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-3">{{ __('#') }}</th>
                            <th class="px-6 py-3">{{ __('Product Name') }}</th>
                            <th class="px-6 py-3">{{ __('SKU') }}</th>
                            <th class="px-6 py-3">{{ __('Quantity') }}</th>
                            <th class="px-6 py-3">{{ __('Price') }}</th>
                            <th class="px-6 py-3">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $index => $item)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-6 py-4">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">{{ $item->product->name_en ?? __('N/A') }}</td>
                                <td class="px-6 py-4">{{ $item->product->sku ?? __('N/A') }}</td>
                                <td class="px-6 py-4">{{ $item->quantity }}</td>
                                <td class="px-6 py-4">{{  $order->currency }} {{ number_format($item->price, 3) }}</td>
                                <td class="px-6 py-4">{{  $order->currency }} {{ number_format($item->total, 3) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="w-full max-w-xs space-y-2 text-sm text-gray-800 dark:text-gray-200">
                    @php
                        $subtotal = $order->items->sum('total');
                        $deliveryCharge = $order->delivery_charge;
                        $grandTotal = $subtotal + $deliveryCharge;
                    @endphp

                    <div class="flex justify-between">
                        <span>{{ __('Subtotal') }}</span>
                        <span>{{  $order->currency }} {{ number_format($subtotal, 3) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Delivery Charge') }}</span>
                        <span>{{  $order->currency }} {{ number_format($deliveryCharge, 3) }}</span>
                    </div>
                    <div class="flex justify-between font-semibold border-t pt-2 border-gray-300 dark:border-gray-600">
                        <span>{{ __('Total') }}</span>
                        <span>{{  $order->currency }} {{ number_format($grandTotal, 3) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end px-6 py-4">
            <button onclick="printInvoice()" class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-700">
                {{ __('Print Invoice') }}
            </button>
        </div>
    </div>
@endsection

<div id="print-area" style="display: none; font-size: 14px; color: black;">
    <h2 style="font-size: 20px; font-weight: bold; margin-bottom: 10px;">{{ __('Invoice') }}</h2>
    <p><strong>{{ __('Order ID') }}:</strong> {{ $order->id }}</p>
    <p><strong>{{ __('Date') }}:</strong> {{ \Carbon\Carbon::parse($order->created_on)->format('M d, Y h:i A') }}</p>
    <p><strong>{{ __('Customer Name') }}:</strong> {{ $order->customer->name }}</p>
    <p><strong>{{ __('Customer Address') }}:</strong> {{ $order->customer->address }}</p>
    <p><strong>{{ __('WhatsApp') }}:</strong> {{ $order->customer->whatsapp_number }}</p>
    <p><strong>{{ __('Order Status') }}:</strong> {{ __($order->status->value) }}</p>

    <table style="width: 100%; margin-top: 20px; border-collapse: collapse; border: 1px solid black;">
        <thead>
            <tr style="background-color: #e5e7eb;">
                <th style="border: 1px solid black; padding: 6px;">{{ __('#') }}</th>
                <th style="border: 1px solid black; padding: 6px;">{{ __('Product') }}</th>
                <th style="border: 1px solid black; padding: 6px;">{{ __('SKU') }}</th>
                <th style="border: 1px solid black; padding: 6px;">{{ __('Qty') }}</th>
                <th style="border: 1px solid black; padding: 6px;">{{ __('Price') }}</th>
                <th style="border: 1px solid black; padding: 6px;">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $index => $item)
                <tr>
                    <td style="border: 1px solid black; padding: 6px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid black; padding: 6px;">{{ $item->product->name_en ?? 'N/A' }}</td>
                    <td style="border: 1px solid black; padding: 6px;">{{ $item->product->sku ?? 'N/A' }}</td>
                    <td style="border: 1px solid black; padding: 6px;">{{ $item->quantity }}</td>
                    <td style="border: 1px solid black; padding: 6px;">{{ $order->currency }}
                        {{ number_format($item->price, 3) }}</td>
                    <td style="border: 1px solid black; padding: 6px;">{{ $order->currency }}
                        {{ number_format($item->total, 3) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div style="margin-top: 20px; width: 300px; float: right;">
        <div style="display: flex; justify-content: space-between; padding: 4px 0;">
            <span>{{ __('Subtotal') }}</span>
            <span>{{ $order->currency }} {{ number_format($subtotal, 3) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 4px 0;">
            <span>{{ __('Delivery Charge') }}</span>
            <span>{{ $order->currency }} {{ number_format($deliveryCharge, 3) }}</span>
        </div>
        <div
            style="display: flex; justify-content: space-between; font-weight: bold; border-top: 2px solid black; padding: 8px 0 0;">
            <span>{{ __('Total') }}</span>
            <span>{{ $order->currency }} {{ number_format($grandTotal, 3) }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>
</div>



<script>
    function printInvoice() {
        const content = document.getElementById('print-area').innerHTML;
        const printWindow = window.open('', '', 'height=700,width=900');
        printWindow.document.write('<html><head><title>Invoice</title>');
        printWindow.document.write('<style>body{font-family:sans-serif;padding:20px;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid black;padding:5px;text-align:left;} </style>');
        printWindow.document.write('</head><body >');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => printWindow.print(), 500);
    }
</script>