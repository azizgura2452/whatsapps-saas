<?php
    $currentLocale = app()->getLocale();
    $lang = get_languages()[$currentLocale] ?? [
        'code' => strtoupper($currentLocale),
        'name' => strtoupper($currentLocale),
        'icon' => '/images/flags/default.svg',
    ];
?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/layouts/partials/locale-switcher.blade.php ENDPATH**/ ?>