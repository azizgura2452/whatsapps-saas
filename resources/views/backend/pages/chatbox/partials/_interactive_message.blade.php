@php $interactive = $raw['interactive'] ?? []; @endphp

@if (in_array($interactive['type'] ?? '', ['button', 'list'], true))
    {{-- Header --}}
    @if (data_get($interactive, 'header.text'))
        <div class="font-semibold mb-1 text-sm">{{ data_get($interactive, 'header.text') }}</div>
    @endif

    {{-- Body --}}
    @if (data_get($interactive, 'body.text'))
        <div class="mb-2">{{ data_get($interactive, 'body.text') }}</div>
    @endif

    {{-- Buttons --}}
    @if (($interactive['type'] ?? '') === 'button' && data_get($interactive, 'action.buttons'))
        <div class="flex flex-wrap gap-2">
            @foreach (data_get($interactive, 'action.buttons', []) as $button)
                <button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">
                    {{ data_get($button, 'reply.title') }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- Lists --}}
    @if (($interactive['type'] ?? '') === 'list' && data_get($interactive, 'action.sections'))
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
    @php $reply = $interactive[$interactive['type']] ?? []; @endphp
    <div class="italic text-sm">
        Selected: <span class="font-semibold">{{ data_get($reply, 'title', data_get($reply, 'id')) }}</span>
    </div>
@else
    <div>{{ $message->content }}</div>
@endif
