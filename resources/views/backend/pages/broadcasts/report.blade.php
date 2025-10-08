@extends('backend.layouts.app')

@section('title', __('Broadcast Report') . ' - ' . config('app.name'))

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Broadcast Report') }}: {{ $broadcast->whatsapp_template_name }}
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
                       href="{{ route('admin.broadcasts.index') }}">
                        {{ __('Broadcasts') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Report') }}</li>
            </ol>
        </nav>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Messages') }}</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</div>
        </div>
        
        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
            <div class="text-sm text-green-600 dark:text-green-400">{{ __('Sent') }}</div>
            <div class="text-2xl font-bold text-green-800 dark:text-green-200">{{ $stats['sent'] }}</div>
        </div>
        
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="text-sm text-blue-600 dark:text-blue-400">{{ __('Delivered') }}</div>
            <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">{{ $stats['delivered'] }}</div>
        </div>
        
        <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
            <div class="text-sm text-purple-600 dark:text-purple-400">{{ __('Read') }}</div>
            <div class="text-2xl font-bold text-purple-800 dark:text-purple-200">{{ $stats['read'] }}</div>
        </div>
        
        <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
            <div class="text-sm text-red-600 dark:text-red-400">{{ __('Failed') }}</div>
            <div class="text-2xl font-bold text-red-800 dark:text-red-200">{{ $stats['failed'] }}</div>
        </div>
    </div>

    {{-- Success Rate --}}
    <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-green-50 dark:from-blue-900/20 dark:to-green-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                    {{ __('Success Rate') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Percentage of successfully delivered messages') }}
                </p>
            </div>
            <div class="text-5xl font-bold text-blue-600 dark:text-blue-400">
                {{ $stats['success_rate'] }}%
            </div>
        </div>
        <div class="mt-4 w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
            <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" 
                 style="width: {{ $stats['success_rate'] }}%"></div>
        </div>
    </div>

    {{-- Messages Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5">
            <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                {{ __('Message Delivery Status') }}
            </h3>
        </div>

        <div class="overflow-x-auto border-t border-gray-100 dark:border-gray-800">
            <table class="w-full dark:text-gray-400">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th class="p-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Customer') }}</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Phone Number') }}</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Status') }}</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Sent At') }}</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('WhatsApp ID') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">
                                {{ $message->conversation->customer->name ?? __('Unknown') }}
                            </td>
                            <td class="p-3 font-mono text-sm">{{ $message->phone_number }}</td>
                            <td class="p-3">
                                @php
                                    $statusColors = [
                                        'sent' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'delivered' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'read' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    ];
                                    $statusColor = $statusColors[$message->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                    @if($message->status === 'sent')
                                        <i class="bi bi-check-circle mr-1"></i>
                                    @elseif($message->status === 'delivered')
                                        <i class="bi bi-check-all mr-1"></i>
                                    @elseif($message->status === 'read')
                                        <i class="bi bi-eye mr-1"></i>
                                    @elseif($message->status === 'failed')
                                        <i class="bi bi-x-circle mr-1"></i>
                                    @endif
                                    {{ ucfirst($message->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-sm">
                                {{ \Carbon\Carbon::createFromTimestamp($message->timestamp)->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="p-3 font-mono text-xs text-gray-500">
                                {{ $message->whatsapp_message_id ? Str::limit($message->whatsapp_message_id, 20) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                {{ __('No messages found for this broadcast') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.broadcasts.index') }}" class="btn-default">
            <i class="bi bi-arrow-left mr-2"></i>
            {{ __('Back to Broadcasts') }}
        </a>
    </div>
</div>
@endsection