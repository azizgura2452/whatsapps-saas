@php $lastMessageDate = null; @endphp

@foreach($messages as $message)
    @php
        $messageDate = \Carbon\Carbon::createFromTimestamp($message->timestamp)->toDateString();
        $raw = json_decode($message->raw_data ?? '[]', true) ?: [];
    @endphp

    {{-- Bubble --}}
    <div class="w-full mb-2 flex {{ $message->direction === 'inbound' ? 'justify-start' : 'justify-end' }}">
        <div class="relative max-w-[75%] text-[15px] leading-snug px-3 py-2 rounded-lg shadow-sm
                {{ $message->direction === 'inbound'
            ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none'
            : 'wa-outgoing text-gray-900 dark:text-white rounded-br-none' }}">

            {{-- MEDIA --}}
            @if(isset($raw['image']) || isset($raw['video']) || isset($raw['document']) || isset($raw['audio']))
                @include('backend.pages.chatbox.partials._media_message', ['raw' => $raw, 'message' => $message])

                {{-- INTERACTIVE --}}
            @elseif (isset($raw['type']) && $raw['type'] === 'interactive')
                @include('backend.pages.chatbox.partials._interactive_message', ['raw' => $raw, 'message' => $message])
                {{-- replicate interactive block same as _chat.blade.php --}}
                {{-- ... --}}

                {{-- TEMPLATE --}}
            @elseif (isset($raw['type']) && $raw['type'] === 'template')
                @include('backend.pages.chatbox.partials._template_message', ['raw' => $raw, 'message' => $message])
                {{-- replicate template block same as _chat.blade.php --}}
                {{-- ... --}}

                {{-- DEFAULT TEXT --}}
            @else
                <div class="whitespace-pre-line">{{ $message->content }}</div>
            @endif

            {{-- Time --}}
            <div class="mt-1 text-[10px] text-gray-500 dark:text-gray-300 text-right">
                {{ \Carbon\Carbon::createFromTimestamp($message->timestamp)->format('h:i A') }}
            </div>
        </div>
    </div>
@endforeach