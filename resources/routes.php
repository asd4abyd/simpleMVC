<?php

return [
    '/' => [
        "method" => [\App\Controllers\StartController::class, 'index'],
        'middlewares' => [\App\Middleware\NotPassMiddleware::class]
    ],


    '/test/{id}' => [
        "method" => [\App\Controllers\StartController::class, 'name'],
        'middlewares' => []
    ],
];