<?php
    $media = $raw['image'] ?? $raw['video'] ?? $raw['document'] ?? $raw['audio'] ?? null;
    $mime = $media['mime_type'] ?? 'application/octet-stream';
    $mediaId = $media['id'] ?? null;
?>
<?php if($mediaId): ?>
    <div id="media-<?php echo e($mediaId); ?>">
        <a href="javascript:void(0)" onclick="handleMediaClick('<?php echo e($mediaId); ?>', '<?php echo e($mime); ?>')"
            class="inline-block px-3 py-1 rounded bg-blue-600 text-white text-sm">
            Preview (<?php echo e($mime); ?>)
        </a>
    </div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/chatbox/partials/_media_message.blade.php ENDPATH**/ ?>