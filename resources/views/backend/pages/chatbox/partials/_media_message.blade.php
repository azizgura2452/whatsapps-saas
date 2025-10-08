@php
    $media = $raw['image'] ?? $raw['video'] ?? $raw['document'] ?? $raw['audio'] ?? null;
    $mime = $media['mime_type'] ?? 'application/octet-stream';
    $mediaId = $media['id'] ?? null;
@endphp
@if($mediaId)
    <div id="media-{{ $mediaId }}">
        <a href="javascript:void(0)" onclick="handleMediaClick('{{ $mediaId }}', '{{ $mime }}')"
            class="inline-block px-3 py-1 rounded bg-blue-600 text-white text-sm">
            Preview ({{ $mime }})
        </a>
    </div>
@endif