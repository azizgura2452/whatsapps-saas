<?php
    $tpl = $raw['template'] ?? [];
    $components = collect($tpl['components'] ?? [])->map(function ($c) {
        $c['type_lc'] = strtolower($c['type'] ?? '');
        return $c;
    });

    $paramText = function ($p) {
        if (!is_array($p)) return (string) $p;
        if (isset($p['text'])) return (string) $p['text'];
        if (isset($p['currency']['fallback_value'])) return (string) $p['currency']['fallback_value'];
        if (isset($p['date_time']['fallback_value'])) return (string) $p['date_time']['fallback_value'];
        if (isset($p['fallback_value'])) return (string) $p['fallback_value'];
        return '';
    };

    $renderText = function ($text, $params) use ($paramText) {
        $flat = array_map($paramText, $params ?? []);
        return preg_replace_callback('/\{\{\s*(\d+)\s*\}\}/', function ($m) use ($flat) {
            $i = max(0, ((int) $m[1]) - 1);
            return $flat[$i] ?? '';
        }, (string) ($text ?? ''));
    };

    $headerComp = $components->firstWhere('type_lc', 'header');
    $bodyComp   = $components->firstWhere('type_lc', 'body');
    $footerComp = $components->firstWhere('type_lc', 'footer');
    $buttonsComp = $components->firstWhere('type_lc', 'button') ?? $components->firstWhere('type_lc', 'buttons');

    $headerText = $headerComp['text'] ?? ($headerComp['format']['text'] ?? null);
    $bodyText   = $bodyComp['text'] ?? null;
    $footerText = $footerComp['text'] ?? null;
    $headerParams = $headerComp['parameters'] ?? [];
    $bodyParams   = $bodyComp['parameters'] ?? [];

    $headerOut = $headerText ? $renderText($headerText, $headerParams) : null;
    $bodyOut   = $bodyText ? $renderText($bodyText, $bodyParams) : null;

    $buttons   = data_get($buttonsComp, 'buttons');
    if (!$buttons) {
        $buttons = data_get($buttonsComp, 'parameters.0.buttons', []);
    }
?>

<?php if($headerOut): ?>
    <div class="font-semibold mb-1 text-sm"><?php echo e($headerOut); ?></div>
<?php endif; ?>

<?php if($bodyOut): ?>
    <div class="mb-2 whitespace-pre-line"><?php echo e($bodyOut); ?></div>
<?php elseif(!empty($tpl['name']) && empty($headerOut) && empty($footerText)): ?>
    <div class="italic text-xs text-gray-600 dark:text-gray-300">
        Template: <?php echo e($tpl['name']); ?><?php echo e(isset($tpl['language']['code']) ? ' · ' . strtoupper($tpl['language']['code']) : ''); ?>

    </div>
<?php endif; ?>

<?php if(is_array($buttons) && count($buttons)): ?>
    <div class="flex flex-wrap gap-2 mt-2">
        <?php $__currentLoopData = $buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $btn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $title = $btn['text'] ?? $btn['title'] ?? data_get($btn, 'reply.title') ?? 'Button'; ?>
            <button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">
                <?php echo e($title); ?>

            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<?php if(!empty($footerText)): ?>
    <div class="mt-2 text-xs text-gray-500 dark:text-gray-300"><?php echo e($footerText); ?></div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/chatbox/partials/_template_message.blade.php ENDPATH**/ ?>