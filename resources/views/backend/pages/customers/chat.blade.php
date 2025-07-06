@extends('backend.layouts.app')

@section('title')
    {{ __('Conversation History') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-7xl md:p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                {{ __('Conversation with') }} {{ ucwords($customer->name) }}
            </h2>
            <nav>
                <ol class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5">
                            {{ __('Home') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-1.5">
                            {{ __('Customers') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-gray-800 dark:text-white/90">{{ ucwords($customer->name) }}</li>
                </ol>
            </nav>
        </div>


        <div class="border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-white/[0.03] max-h-[70vh] overflow-y-auto"
            style="background-image: url('{{ asset('images/' . ltrim('whatsapp_bg.jpg', '/')) }}'); background-position: center; background-size: 40%; background-attachment: fixed;
                                            background-color: #003a00;">
            <div class="flex items-center gap-4 p-4 bg-green-600 text-white rounded-t-2xl shadow-sm"
                style="background-color: #128c7e; color: #fff">
                <div class="flex-shrink-0">
                    @if (!empty($customer->name))
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center font-semibold text-2xl text-white border-2 border-white">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-full border-2 border-white"></div>
                    @endif
                </div>
                <div>
                    <div class="text-lg font-semibold leading-snug">
                        {{ !empty($customer->name) ? ucwords($customer->name) : $customer->whatsapp_number }}
                    </div>
                    @if (!empty($customer->name))
                        <div class="text-sm text-white/90">
                            {{ $customer->whatsapp_number }}
                        </div>
                    @endif
                </div>
            </div>

            <div id="chat-container">
                @php
                    $lastMessageDate = null;
                @endphp

                @forelse($messages as $message)
                        @php
                            $messageDate = \Carbon\Carbon::createFromTimestamp($message->timestamp)->toDateString();
                        @endphp

                        @if ($lastMessageDate !== $messageDate)
                            <div class="text-center my-4">
                                <span
                                    class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs px-3 py-1 rounded-full">
                                    {{ \Carbon\Carbon::parse($messageDate)->format('F j, Y') }}
                                </span>
                            </div>
                            @php
                                $lastMessageDate = $messageDate;
                            @endphp
                        @endif
                        <div class="p-4 w-full mb-4 flex {{ $message->direction === 'inbound' ? 'justify-start' : 'justify-end' }}">
                            <div class="max-w-[75%] px-4 py-2 rounded-xl shadow-sm text-sm leading-relaxed whitespace-pre-line
                                                                                                                                                        {{ $message->direction === 'inbound'
                    ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-100 rounded-bl-none'
                    : 'bg-green-100 text-gray-900 dark:bg-green-800 dark:text-white rounded-br-none' }}">
                                @php
                                    $raw = json_decode($message->raw_data, true);
                                @endphp

                                @if (isset($raw['type']) && $raw['type'] === 'interactive')
                                    @php
                                        $interactive = $raw['interactive'];
                                    @endphp

                                    {{-- Handle Button and List Interactive Messages --}}
                                    @if (in_array($interactive['type'], ['button', 'list']))
                                        {{-- Header --}}
                                        @if(isset($interactive['header']['text']))
                                            <div class="font-semibold mb-1 text-sm">
                                                {{ $interactive['header']['text'] }}
                                            </div>
                                        @endif

                                        {{-- Body --}}
                                        @if(isset($interactive['body']['text']))
                                            <div class="mb-2">
                                                {{ $interactive['body']['text'] }}
                                            </div>
                                        @endif

                                        {{-- Buttons --}}
                                        @if($interactive['type'] === 'button' && isset($interactive['action']['buttons']))
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($interactive['action']['buttons'] as $button)
                                                    <button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">
                                                        {{ $button['reply']['title'] }}
                                                    </button>
                                                @endforeach
                                            </div>

                                            {{-- Lists --}}
                                        @elseif($interactive['type'] === 'list' && isset($interactive['action']['sections']))
                                            <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
                                                <div class="font-medium text-sm text-gray-700 dark:text-gray-200 mb-2">
                                                    {{ $interactive['action']['button'] ?? 'Options' }}
                                                </div>
                                                <ul class="space-y-1 text-sm text-gray-800 dark:text-gray-100">
                                                    @foreach ($interactive['action']['sections'] as $section)
                                                        @foreach ($section['rows'] as $row)
                                                            <li>
                                                                {{ $row['title'] }}
                                                            </li>
                                                        @endforeach
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        {{-- Handle Button or List Reply from User --}}
                                    @elseif(in_array($interactive['type'], ['button_reply', 'list_reply']))
                                        @php
                                            $reply = $interactive[$interactive['type']];
                                        @endphp
                                        <div class="italic text-sm text-gray-800 dark:text-gray-100">
                                            Selected: <span class="font-semibold">{{ $reply['title'] ?? $reply['id'] }}</span>
                                        </div>
                                    @else
                                        <div>{{ $message->content }}</div>
                                    @endif
                                @elseif (isset($raw['type']) && $raw['type'] === 'template' && isset($raw['template']['components']))
                                    @php
                                        $mpmComponent = collect($raw['template']['components'])
                                            ->first(fn($comp) => $comp['type'] === 'button' && ($comp['sub_type'] ?? null) === 'mpm');
                                    @endphp

                                    @if ($mpmComponent && isset($mpmComponent['parameters'][0]['action']['sections']))
                                        <div class="mb-2 font-semibold text-sm text-gray-800 dark:text-gray-100">
                                            Multi-Product Message (MPM):
                                        </div>
                                        @foreach ($mpmComponent['parameters'][0]['action']['sections'] as $section)
                                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                                <strong>{{ $section['title'] ?? 'Section' }}:</strong>
                                            </div>
                                            <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
                                                <ul class="text-sm text-gray-800 dark:text-gray-100 mb-3">
                                                    @foreach ($section['product_items'] as $product)
                                                        <li>{{ $product['product_retailer_id'] }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                        @endforeach
                                    @endif
                                @else
                                    <div>{{ $message->content }}</div>
                                @endif

                                <div class="text-end text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ \Carbon\Carbon::createFromTimestamp($message->timestamp)->format('Y-m-d H:i') }}
                                </div>
                            </div>
                        </div>
                @empty
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No messages found.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

<script>
    let lastTimestamp = {{ $messages->last()?->timestamp ?? 0 }};
    const fetchUrl = "{{ route('admin.customers.messages', $customer->id) }}";

    function formatMessage(message) {
        const container = document.createElement('div');
        container.className = 'p-4 w-full mb-4 flex ' + (message.direction === 'inbound' ? 'justify-start' : 'justify-end');

        const bubble = document.createElement('div');
        bubble.className =
            'max-w-[75%] px-4 py-2 rounded-xl shadow-sm text-sm leading-relaxed whitespace-pre-line ' +
            (message.direction === 'inbound'
                ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-100 rounded-bl-none'
                : 'bg-green-100 text-gray-900 dark:bg-green-800 dark:text-white rounded-br-none');

        let content = '';
        let raw = {};

        try {
            raw = typeof message.raw_data === 'string' ? JSON.parse(message.raw_data) : message.raw_data;
        } catch (e) {
            raw = {};
        }

        if (raw.type === 'interactive') {
            const interactive = raw.interactive;

            if (['button', 'list'].includes(interactive.type)) {
                if (interactive.header?.text) {
                    content += `<div class="font-semibold mb-1 text-sm">${interactive.header.text}</div>`;
                }
                if (interactive.body?.text) {
                    content += `<div class="mb-2">${interactive.body.text}</div>`;
                }

                if (interactive.type === 'button' && interactive.action?.buttons) {
                    content += '<div class="flex flex-wrap gap-2">';
                    interactive.action.buttons.forEach(button => {
                        content += `<button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">${button.reply.title}</button>`;
                    });
                    content += '</div>';
                }

                if (interactive.type === 'list' && interactive.action?.sections) {
                    content += '<div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">';
                    content += `<div class="font-medium text-sm text-gray-700 dark:text-gray-200 mb-2">${interactive.action.button ?? 'Options'}</div>`;
                    content += '<ul class="space-y-1 text-sm text-gray-800 dark:text-gray-100">';
                    interactive.action.sections.forEach(section => {
                        section.rows.forEach(row => {
                            content += `<li>${row.title}</li>`;
                        });
                    });
                    content += '</ul></div>';
                }
            } else if (['button_reply', 'list_reply'].includes(interactive.type)) {
                const reply = interactive[interactive.type];
                content += `<div class="italic text-sm text-gray-800 dark:text-gray-100">Selected: <span class="font-semibold">${reply.title ?? reply.id}</span></div>`;
            } else {
                content += `<div>${message.content}</div>`;
            }
        } else if (raw.type === 'template' && Array.isArray(raw.template?.components)) {
            const mpmComponent = raw.template.components.find(comp => comp.type === 'button' && comp.sub_type === 'mpm');
            if (mpmComponent?.parameters?.[0]?.action?.sections) {
                content += '<div class="mb-2 font-semibold text-sm text-gray-800 dark:text-gray-100">Multi-Product Message (MPM):</div>';
                mpmComponent.parameters[0].action.sections.forEach(section => {
                    content += `<div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><strong>${section.title ?? 'Section'}:</strong></div>`;
                    content += '<div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">';
                    content += '<ul class="text-sm text-gray-800 dark:text-gray-100 mb-3">';
                    section.product_items.forEach(product => {
                        content += `<li>${product.product_retailer_id}</li>`;
                    });
                    content += '</ul></div>';
                });
            } else {
                content += `<div>${message.content}</div>`;
            }
        } else {
            content += `<div>${message.content}</div>`;
        }

        content += `<div class="text-end text-xs text-gray-500 dark:text-gray-400 mt-1">${new Date(message.timestamp * 1000).toLocaleString()}</div>`;

        bubble.innerHTML = content;
        container.appendChild(bubble);
        return container;
    }


    function fetchMessages() {
        fetch(`${fetchUrl}?since=${lastTimestamp}`)
            .then(response => response.json())
            .then(data => {
                const newMessages = data.messages;
                if (newMessages.length > 0) {
                    lastTimestamp = newMessages.at(-1).timestamp;

                    const chatContainer = document.querySelector('#chat-container');

                    const isNearBottom = chatContainer.scrollHeight - chatContainer.scrollTop <= chatContainer.clientHeight + 50;

                    newMessages.forEach(msg => {
                        chatContainer.appendChild(formatMessage(msg));
                    });

                    // Only scroll to bottom if user was already near the bottom
                    if (isNearBottom) {
                        requestAnimationFrame(() => {
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        });
                    }
                }
            })
            .catch(error => console.error("Message fetch failed:", error));
    }

    setInterval(fetchMessages, 5000); // poll every 5 seconds
</script>