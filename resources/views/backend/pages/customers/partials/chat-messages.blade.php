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