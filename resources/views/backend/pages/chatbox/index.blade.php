@extends('backend.layouts.app')

@section('title')
    {{ __('WhatsApp Chatbox') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-3 mx-auto max-w-(--breakpoint-2xl)">
        <div
            class="h-[calc(100vh-7rem)] md:h-[calc(100vh-6rem)] flex border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-white/[0.03]">

            {{-- Sidebar --}}
            {{-- Sidebar --}}
            <aside class="w-[200px] shrink-0 border-r border-gray-200 dark:border-gray-800 flex flex-col"
                style="width:320px; height: 90vh">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between"
                    style="background:var(--wa-green); color:#fff;">
                    <div class="flex items-center gap-3">
                        <div class="text-sm leading-tight">
                            <div class="font-semibold">WhatsApp Chats</div>
                        </div>
                    </div>

                </div>

                {{-- search --}}
                <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                    <div class="relative">
                        <input type="text" name="q" value="{{ $q }}" form="wa-search-form" placeholder="{{ __('Search') }}"
                            class="w-full pl-3 pr-10 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 border border-transparent focus:border-gray-300 dark:focus:border-gray-700 text-sm">

                        {{-- CTA search icon (submits the form) --}}
                        <button type="submit" form="wa-search-form"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200/60 dark:hover:bg-gray-700/70 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300 dark:focus:ring-gray-600"
                            aria-label="Search" title="Search" style="transform: translateX(-30px)">
                            <svg class="w-5 h-5">
                                <use href="#ico-search" />
                            </svg>
                        </button>
                    </div>

                    <form id="wa-search-form" method="GET" action="{{ route('admin.whatsapp.chatbox') }}"></form>
                </div>



                {{-- IMPORTANT: make page/hasMore dynamic --}}
                <div class="flex-1 overflow-y-auto" id="chatbox-sidebar" data-page="{{ $customers->currentPage() }}"
                    data-has-more="{{ $customers->hasMorePages() ? '1' : '0' }}">
                    @include('backend.pages.chatbox._customers', ['customers' => $customers, 'selectedId' => $selectedId])
                    {{-- Sentinel for infinite scroll --}}
                    <div id="sidebar-infinite-sentinel" class="h-6"></div>
                </div>

                <div id="sidebar-loading" class="hidden text-center py-2 text-gray-500">Loading...</div>

                <noscript>
                    <div class="border-t border-gray-200 dark:border-gray-800 p-3">
                        {{ $customers->withQueryString()->links() }}
                    </div>
                </noscript>
            </aside>


            {{-- Right Pane --}}
            <section class="flex-1 flex flex-col">
                <div id="chatbox-pane" class="flex-1 overflow-hidden">
                    @if($selectedId)
                        <div id="chatbox-loading"
                            class="h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                            {{ __('Loading chat…') }}
                        </div>
                    @else
                        <div class="h-full grid place-items-center text-gray-500 dark:text-gray-400 p-6 text-center">
                            <div>
                                <div class="text-xl font-semibold mb-2">{{ __('Select a conversation') }}</div>
                                <div>{{ __('Choose a customer from the left to view messages') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* WhatsApp-ish colors */
        :root {
            --wa-green: #128C7E;
            /* top bar */
            --wa-teal: #25D366;
            /* accents */
            --wa-outgoing: #DCF8C6;
            /* outgoing bubble (light) */
            --wa-bg: #F0F2F5;
            /* chat bg */
        }

        .dark :root {
            --wa-outgoing: #075e54;
            /* dark outgoing */
            --wa-bg: #0b141a;
            /* dark chat bg */
        }

        /* WhatsApp patterned chat bg (subtle dots) */
        #chat-container {
            background-color: var(--wa-bg);
            background-image:
                radial-gradient(rgba(0, 0, 0, 0.03) 1px, transparent 1px);
            background-size: 6px 6px;
        }

        /* Thin scrollbars like WhatsApp */
        #chatbox-sidebar::-webkit-scrollbar,
        #chat-container::-webkit-scrollbar {
            width: 8px;
        }

        #chatbox-sidebar::-webkit-scrollbar-thumb,
        #chat-container::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, .15);
            border-radius: 999px;
        }
    </style>

    <svg xmlns="http://www.w3.org/2000/svg" style="display:none">
        <symbol id="ico-status" viewBox="0 0 24 24">
            <path fill="currentColor" d="M12 8v4l3 3 1-1-3-2.5V8z" />
            <path fill="currentColor"
                d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2m0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16" />
        </symbol>
        <symbol id="ico-chat" viewBox="0 0 24 24">
            <path fill="currentColor" d="M4 4h16v12H7l-3 3z" />
        </symbol>
        <symbol id="ico-more" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M6 12a2 2 0 1 0 0-4 2 2 0 0 0 0 4m6 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4m6 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
        </symbol>
        <symbol id="ico-search" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M10 18a8 8 0 1 1 6.32-3.09l4.39 4.39l-1.41 1.41l-4.39-4.39A8 8 0 0 1 10 18m0-2a6 6 0 1 0 0-12a6 6 0 0 0 0 12" />
        </symbol>
        <symbol id="ico-attach" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M7 17a4 4 0 0 1 0-5.66l6.3-6.3a3 3 0 1 1 4.24 4.24L10.24 16.8a2 2 0 1 1-2.83-2.83l6-6l1.41 1.41l-6 6a.5.5 0 0 0 .71.71l7.3-7.3a1 1 0 1 0-1.41-1.41L8.41 13.4A3.5 3.5 0 1 0 13.36 18.3l6.3-6.3l1.41 1.41l-6.3 6.3A5 5 0 1 1 7 17" />
        </symbol>
        <symbol id="ico-emoji" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M12 22A10 10 0 1 1 12 2a10 10 0 0 1 0 20m0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16m4-6a4 4 0 0 1-8 0h8M9 9.5A1.5 1.5 0 1 0 9 6.5A1.5 1.5 0 0 0 9 9.5m6 0A1.5 1.5 0 1 0 15 6.5A1.5 1.5 0 0 0 15 9.5" />
        </symbol>
        <symbol id="ico-mic" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M12 14a3 3 0 0 0 3-3V6a3 3 0 1 0-6 0v5a3 3 0 0 0 3 3m5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.92V21h2v-3.08A7 7 0 0 0 19 11z" />
        </symbol>
        <symbol id="ico-send" viewBox="0 0 24 24">
            <path fill="currentColor" d="M2 21V3l21 9-9 3-3 9-3-9 9-3-15-6v6l12 3-12 3v6z" />
        </symbol>

    </svg>

@endpush

@push('styles')
    <style>
        :root {
            --wa-outgoing: #DCF8C6;
        }

        .dark :root {
            --wa-outgoing: #075E54;
        }

        .wa-outgoing {
            background-color: var(--wa-outgoing) !important;
        }
    </style>
@endpush


@push('scripts')
    <script>
        (function () {
            const pane = document.getElementById('chatbox-pane');
            let activeCustomerId = @json($selectedId ?: null);
            let lastTimestamp = 0;
            let pollTimer = null;

            const routes = {
                chat: (id) => `{{ route('admin.whatsapp.chatbox.chat', ':id') }}`.replace(':id', id),
                poll: (id) => `{{ route('admin.customers.messages', ':id') }}`.replace(':id', id), // reuse existing
            };

            function startPolling() {
                stopPolling();
                if (!activeCustomerId) return;
                pollTimer = setInterval(fetchNewMessages, 5000);
            }
            function stopPolling() {
                if (pollTimer) clearInterval(pollTimer);
                pollTimer = null;
            }

            function bindChatBehaviors() {
                const tsMeta = pane.querySelector('[data-last-timestamp]');
                lastTimestamp = tsMeta ? parseInt(tsMeta.getAttribute('data-last-timestamp')) || 0 : 0;

                const container = pane.querySelector('#chat-container');
                if (container) container.scrollTop = container.scrollHeight;
            }

            function formatMessage(message) {
                const wrapper = document.createElement('div');
                wrapper.className = 'p-4 w-full mb-4 flex ' + (message.direction === 'inbound' ? 'justify-start' : 'justify-end');

                const bubble = document.createElement('div');
                bubble.className = 'max-w-[75%] px-3 py-1.5 rounded-xl shadow-sm text-sm leading-normal whitespace-pre-line ' +
                    (message.direction === 'inbound'
                        ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-100 rounded-bl-none'
                        : 'bg-green-100 text-gray-900 dark:bg-green-800 dark:text-white rounded-br-none');

                let raw = {};
                try { raw = typeof message.raw_data === 'string' ? JSON.parse(message.raw_data) : (message.raw_data || {}); } catch (e) { raw = {}; }

                let html = '';
                if (raw.type === 'interactive') {
                    const interactive = raw.interactive || {};
                    if (['button', 'list'].includes(interactive.type)) {
                        if (interactive.header?.text) html += `<div class="font-semibold mb-1 text-sm">${interactive.header.text}</div>`;
                        if (interactive.body?.text) html += `<div class="mb-2">${interactive.body.text}</div>`;
                        if (interactive.type === 'button' && interactive.action?.buttons) {
                            html += `<div class="flex flex-wrap gap-2">` +
                                interactive.action.buttons.map(b => `<button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">${b.reply?.title ?? ''}</button>`).join('') +
                                `</div>`;
                        }
                        if (interactive.type === 'list' && interactive.action?.sections) {
                            html += `<div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">` +
                                `<div class="font-medium text-sm text-gray-700 dark:text-gray-200 mb-2">${interactive.action.button ?? 'Options'}</div>` +
                                `<ul class="space-y-1 text-sm text-gray-800 dark:text-gray-100">` +
                                interactive.action.sections.flatMap(s => s.rows.map(r => `<li>${r.title}</li>`)).join('') +
                                `</ul></div>`;
                        }
                    } else if (['button_reply', 'list_reply'].includes(interactive.type)) {
                        const reply = interactive[interactive.type] || {};
                        html += `<div class="italic text-sm text-gray-800 dark:text-gray-100">Selected: <span class="font-semibold">${reply.title ?? reply.id ?? ''}</span></div>`;
                    } else {
                        html += `<div>${message.content ?? ''}</div>`;
                    }
                } else if (raw.type === 'template' && Array.isArray(raw.template?.components)) {
                    const mpm = raw.template.components.find(c => c.type === 'button' && c.sub_type === 'mpm');
                    if (mpm?.parameters?.[0]?.action?.sections) {
                        html += `<div class="mb-2 font-semibold text-sm text-gray-800 dark:text-gray-100">Multi-Product Message (MPM):</div>`;
                        mpm.parameters[0].action.sections.forEach(section => {
                            html += `<div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><strong>${section.title ?? 'Section'}:</strong></div>`;
                            html += `<div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600"><ul class="text-sm text-gray-800 dark:text-gray-100 mb-3">`;
                            (section.product_items || []).forEach(p => html += `<li>${p.product_retailer_id}</li>`);
                            html += `</ul></div>`;
                        });
                    } else {
                        html += `<div>${message.content ?? ''}</div>`;
                    }
                } else {
                    html += `<div>${message.content ?? ''}</div>`;
                }

                html += `<div class="text-end text-xs text-gray-500 dark:text-gray-400 mt-1">${new Date((+message.timestamp) * 1000).toLocaleString()}</div>`;
                bubble.innerHTML = html;
                wrapper.appendChild(bubble);
                return wrapper;
            }

            async function fetchNewMessages() {
                if (!activeCustomerId) return;

                try {
                    const res = await fetch(`${routes.poll(activeCustomerId)}?since=${lastTimestamp}`);
                    const data = await res.json();

                    if (!data.count) return; // no new messages

                    const container = pane.querySelector('#chat-container');
                    if (!container) return;

                    const isNearBottom =
                        container.scrollHeight - container.scrollTop <= container.clientHeight + 50;

                    // Append new messages' HTML
                    container.insertAdjacentHTML('beforeend', data.html);

                    // Update last timestamp
                    lastTimestamp = data.lastTs;

                    // Auto-scroll if user is near bottom
                    if (isNearBottom) {
                        container.scrollTop = container.scrollHeight;
                    }
                } catch (err) {
                    console.error('Polling failed:', err);
                }
            }


            async function loadChat(customerId, pushState = false) {
                if (!customerId) return;

                activeCustomerId = customerId;
                stopPolling();

                pane.innerHTML = `<div class="h-full flex items-center justify-center text-gray-500 dark:text-gray-400">Loading chat…</div>`;
                try {
                    const res = await fetch(routes.chat(customerId), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = await res.text();
                    pane.innerHTML = html;
                    bindChatBehaviors();
                    startPolling();
                    if (pushState) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('customer_id', customerId);
                        window.history.replaceState({}, '', url.toString());
                    }
                } catch (e) {
                    pane.innerHTML = `<div class="h-full grid place-items-center text-red-500">Failed to load chat.</div>`;
                    console.error(e);
                }
            }

            // EXPOSE so other blocks can call it
            window.loadChat = loadChat;

            // Initial load if a customer is preselected
            if (activeCustomerId) {
                loadChat(activeCustomerId, false);
            }

            // Event delegation for clicking customer rows (works for appended rows too)
            const sidebar = document.getElementById('chatbox-sidebar');
            sidebar.addEventListener('click', (e) => {
                const btn = e.target.closest('.chatbox-customer-btn');
                if (!btn) return;
                const id = btn.getAttribute('data-customer-id');

                // visual active state
                sidebar.querySelectorAll('.chatbox-customer-btn').forEach(x => x.classList.remove('bg-gray-50', 'dark:bg-gray-800'));
                btn.classList.add('bg-gray-50', 'dark:bg-gray-800');

                loadChat(id, true);
            });

        })();
    </script>

    <script>
        (function () {
            const sidebar = document.getElementById('chatbox-sidebar');
            const loader = document.getElementById('sidebar-loading');
            let sentinel = document.getElementById('sidebar-infinite-sentinel');

            if (!sidebar) return; // safety

            let loading = false;
            let nextPage = (parseInt(sidebar.dataset.page || '1', 10) || 1) + 1;
            let hasMore = sidebar.dataset.hasMore === '1';
            const q = @json($q ?? '');

            // Only after the user scrolls inside the sidebar, we allow IO to trigger
            let userScrolled = false;
            sidebar.addEventListener('scroll', () => { userScrolled = true; });

            async function loadMore() {
                if (loading || !hasMore) return;

                loading = true;
                loader.classList.remove('hidden');

                try {
                    const url = `{{ route('admin.whatsapp.chatbox') }}?page=${nextPage}&q=${encodeURIComponent(q)}`;
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = (await res.text()).trim();

                    if (html) {
                        // Insert new customers above the sentinel
                        sentinel.insertAdjacentHTML('beforebegin', html);

                        // Recreate sentinel so IO re-triggers on the new end
                        const newSentinel = document.createElement('div');
                        newSentinel.id = 'sidebar-infinite-sentinel';
                        newSentinel.className = 'h-6';

                        io.unobserve(sentinel);
                        sentinel.replaceWith(newSentinel);
                        sentinel = newSentinel;
                        io.observe(sentinel);

                        nextPage++;
                    } else {
                        hasMore = false;
                    }
                } catch (err) {
                    console.error('Failed to load more customers', err);
                } finally {
                    loader.classList.add('hidden');
                    loading = false;
                }
            }

            // Observe the sentinel INSIDE the sidebar scroller
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!userScrolled) return;      // don’t autoload on first paint
                    if (entry.isIntersecting) loadMore();
                });
            }, {
                root: sidebar,                     // <- the scroll container
                rootMargin: '0px 0px 120px 0px',
                threshold: 0
            });

            io.observe(sentinel);

            // Fallback: near-bottom check on sidebar scroll
            sidebar.addEventListener('scroll', () => {
                const nearBottom = sidebar.scrollTop + sidebar.clientHeight >= sidebar.scrollHeight - 140;
                if (nearBottom) loadMore();
            });
        })();
    </script>


@endpush