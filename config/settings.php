<?php
return array_replace_recursive([
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header

    // Renderer settings
    'renderer' => [
        'template_path' => __DIR__ . '/../templates/',
    ],

    // Monolog settings
    'logger' => [
        'name' => 'web-fonts-repository',
        'path' => __DIR__ . '/../logs/app.log',
        'level' => \Monolog\Logger::DEBUG,
    ],

    // List of webfonts
    'fonts' => [],
], require __DIR__ . '/settings-local.php');
