<?php 

return [
    // Required settings
    'site_url' => $_ENV['APP_URL'] ?? '/', 
    'site_title' => $_ENV['SITE_NAME'] ?? 'Stog',
    'logo_path' => '/assets/images/flick.svg',
    'show_captions' => false,
    'show_copyright' => true

    // Optional settings
];