<?php
return array_replace_recursive([
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header

    // Renderer settings
    'renderer' => [
        'template_path' => __DIR__ . '/../templates/',
    ],

    // Logger settings
    'logger' => [
        'path' => __DIR__ . '/../logs/app.log',
        'level' => 'debug',
    ],

    // List of webfonts
    'fonts' => [],

    // How long should browsers keep generated CSS in cache (in seconds)
    'cssHttpCacheAge' => 2678400,
], require __DIR__ . '/settings-local.php');
