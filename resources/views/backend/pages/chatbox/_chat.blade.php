@push('styles')
  <style>
    /* Force outgoing (your) bubble background like WhatsApp */
    :root {
      --wa-outgoing: #DCF8C6;
    }

    /* light green */
    .dark :root {
      --wa-outgoing: #075E54;
    }

    /* dark mode green */
    .wa-outgoing {
      background-color: var(--wa-outgoing) !important;
    }
  </style>
@endpush

@php
  $lastMessageDate = null;
  $lastTs = (int) ($messages->last()->timestamp ?? 0);
@endphp

{{-- FIXED HEIGHT + SCROLL (no Tailwind height): the wrapper is 90vh --}}
<div class="flex flex-col" style="height:90vh;">

  {{-- Chat Header --}}
  <div
    class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 grid place-items-center font-semibold"
        style="justify-content:center;align-items:center;font-size:24px;">
        <i class="fa fa-user"></i>
      </div>
      <div class="leading-tight">
        <div class="font-semibold">
          {{ $customer->name ? ucwords($customer->name) : $customer->whatsapp_number }}
        </div>
      </div>
    </div>
  </div>

  {{-- Chat Body (scroll container) --}}
  <div id="chat-container" class="p-4 md:p-5" {{-- keep padding; no Tailwind height --}}
    data-last-timestamp="{{ $lastTs }}" style="
      flex: 1 1 auto;                      /* fill remaining height */
      overflow-y: auto;                    /* make this the scroller */
      background-color:#F0F2F5;
      background-image:url('{{ asset('images/' . ltrim('whatsapp_bg.jpg', '/')) }}'),
                       radial-gradient(rgba(0,0,0,0.03) 1px, transparent 1px);
      background-repeat:repeat;
      background-size:40%, 6px 6px;
      background-position:center, 0 0;
    ">
    @forelse($messages as $message)
      @php
        $messageDate = \Carbon\Carbon::createFromTimestamp($message->timestamp)->toDateString();
      @endphp

      {{-- Date chip --}}
      @if ($lastMessageDate !== $messageDate)
        <div class="text-center my-4">
          <span
            class="inline-block bg-gray-200/80 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs px-3 py-1 rounded-full">
            {{ \Carbon\Carbon::parse($messageDate)->format('F j, Y') }}
          </span>
        </div>
        @php $lastMessageDate = $messageDate; @endphp
      @endif

      {{-- Bubble --}}
      <div class="w-full mb-2 flex {{ $message->direction === 'inbound' ? 'justify-start' : 'justify-end' }}">
        <div class="relative max-w-[75%] text-[15px] leading-snug px-3 py-2 rounded-lg shadow-sm
                          {{ $message->direction === 'inbound'
      ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none'
      : 'wa-outgoing text-gray-900 dark:text-white rounded-br-none' }}">
          @php $raw = json_decode($message->raw_data ?? '[]', true) ?: []; @endphp

          {{-- MEDIA CHECK --}}
          @if(isset($raw['image']) || isset($raw['video']) || isset($raw['document']) || isset($raw['audio']))
            @include('backend.pages.chatbox.partials._media_message', ['raw' => $raw, 'message' => $message])


            {{-- INTERACTIVE --}}
          @elseif (isset($raw['type']) && $raw['type'] === 'interactive')
            @php $interactive = $raw['interactive'] ?? []; @endphp
            @if (in_array($interactive['type'] ?? '', ['button', 'list'], true))
              @if (data_get($interactive, 'header.text'))
                <div class="font-semibold mb-1 text-sm">{{ data_get($interactive, 'header.text') }}</div>
              @endif
              @if (data_get($interactive, 'body.text'))
                <div class="mb-2">{{ data_get($interactive, 'body.text') }}</div>
              @endif
              @if (($interactive['type'] ?? '') === 'button' && data_get($interactive, 'action.buttons'))
                <div class="flex flex-wrap gap-2">
                  @foreach (data_get($interactive, 'action.buttons', []) as $button)
                    <button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">
                      {{ data_get($button, 'reply.title') }}
                    </button>
                  @endforeach
                </div>
              @elseif (($interactive['type'] ?? '') === 'list' && data_get($interactive, 'action.sections'))
                <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
                  <div class="font-medium text-sm text-gray-700 dark:text-gray-200 mb-2">
                    {{ data_get($interactive, 'action.button', 'Options') }}
                  </div>
                  <ul class="space-y-1 text-sm text-gray-800 dark:text-gray-100">
                    @foreach (data_get($interactive, 'action.sections', []) as $section)
                      @foreach (data_get($section, 'rows', []) as $row)
                        <li>{{ data_get($row, 'title') }}</li>
                      @endforeach
                    @endforeach
                  </ul>
                </div>
              @endif
            @elseif (in_array($interactive['type'] ?? '', ['button_reply', 'list_reply'], true))
              @include('backend.pages.chatbox.partials._interactive_message', ['raw' => $raw, 'message' => $message])
            @else
              <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
                {{ $message->content }}
              </div>
            @endif

            {{-- TEMPLATE --}}
          @elseif (isset($raw['type']) && $raw['type'] === 'template')
            @include('backend.pages.chatbox.partials._template_message', ['raw' => $raw, 'message' => $message])


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
    @empty
      <p class="text-center text-sm text-gray-500 dark:text-gray-400 p-6">{{ __('No messages found.') }}</p>
    @endforelse
  </div>

  {{-- Composer --}}
  <div class="px-3 py-2 bg-[#F0F2F5] dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="flex items-center gap-2">
      <form class="flex-1 flex items-center gap-2" onsubmit="return false;">
        <input id="chat-message" type="text" placeholder="Type a message..."
          class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-2 text-sm"
          onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();sendMessage({{ $customer->id }});}">
        <button type="button" id="sendBtn" class="btn-primary" onclick="sendMessage({{ $customer->id }})">
          {{-- <i class="fa fa-paper-plane"></i> --}}
          {{-- Using Bootstrap Icons to match rest of the admin --}}
          <i class="bi bi-send"></i>
        </button>
      </form>
    </div>
  </div>

</div>