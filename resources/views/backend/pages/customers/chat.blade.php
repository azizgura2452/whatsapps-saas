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

        {{-- Chat wrapper --}}
        <div class="border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-white/[0.03]"
             style="height: 80vh;">
            {{-- 👉 reuse the chat partial --}}
            @include('backend.pages.chatbox._chat', [
                'customer' => $customer,
                'messages' => $messages
            ])
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
  let lastTimestamp = {{ $messages->last()?->timestamp ?? 0 }};
  const fetchUrl = "{{ route('admin.customers.messages', $customer->id) }}";

  function getChatEl() {
    return document.getElementById('chat-container');
  }

  function scrollToBottom() {
    const el = getChatEl();
    if (!el) return;
    el.scrollTop = el.scrollHeight;
  }

  // Scroll after layout, after load, and with a couple of retries (handles images/fonts)
  function scrollToBottomSoon() {
    requestAnimationFrame(scrollToBottom);
    setTimeout(scrollToBottom, 120);
    setTimeout(scrollToBottom, 400);
    setTimeout(scrollToBottom, 800);
  }

  // Initial scroll on first paint + after images
  document.addEventListener('DOMContentLoaded', scrollToBottomSoon);
  window.addEventListener('load', scrollToBottomSoon);

  // If the container is replaced by partials later, try again once it exists
  if (!getChatEl()) {
    const readyTimer = setInterval(() => {
      if (getChatEl()) { clearInterval(readyTimer); scrollToBottomSoon(); }
    }, 50);
    setTimeout(() => clearInterval(readyTimer), 3000);
  }

  // Also keep auto-scrolling only when the user was already near bottom
  function fetchMessages() {
    fetch(`${fetchUrl}?since=${lastTimestamp}`)
      .then(res => res.json())
      .then(data => {
        if (!data.count) return;

        const container = getChatEl();
        if (!container) return;

        const isNearBottom =
          container.scrollHeight - container.scrollTop <= container.clientHeight + 50;

        // Append new messages html (rendered by _messages.blade.php)
        container.insertAdjacentHTML('beforeend', data.html);

        lastTimestamp = data.lastTs;

        if (isNearBottom) scrollToBottomSoon();
      })
      .catch(err => console.error("Polling failed:", err));
  }

  setInterval(fetchMessages, 5000); // poll every 5s
})();
</script>
@endpush
