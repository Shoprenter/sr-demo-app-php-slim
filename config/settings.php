<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'app' => [
            'shoprenter_url' => $_ENV['SHOPRENTER_URL'] ?? 'shoprenter.hu',
            'client_id' => $_ENV['CLIENT_ID'],
            'client_secret' => $_ENV['SECRET_ID'],
            'app_id' => $_ENV['APP_ID'],
            'recurringCharge' => [
                'successUrl' => $_ENV['APP_URL'] . $_ENV['RECURRING_CHARGE_SUCCESS_URL'],
                'failedUrl' => $_ENV['APP_URL'] . $_ENV['RECURRING_CHARGE_FAILED_URL'],
                'notificationUrl' => $_ENV['APP_URL'] . $_ENV['RECURRING_CHARGE_NOTIFICATION_URL'],
                'planId' => 1,
                'test' => true,
            ],
            'oneTimeCharge' => [
                'successUrl' => $_ENV['APP_URL'] . $_ENV['ONE_TIME_CHARGE_SUCCESS_URL'],
                'failedUrl' => $_ENV['APP_URL'] . $_ENV['ONE_TIME_CHARGE_FAILED_URL'],
                'notificationUrl' => $_ENV['APP_URL'] . $_ENV['ONE_TIME_CHARGE_NOTIFICATION_URL'],
                'name' => 'ACME alkalmazás megvétele',
                'netPrice' => 10000,
                'test' => true
            ]
        ],
    ],
];
