<?php

// Возвращаем массив конфигурации
return [
    // Ключ массива - имя события
    'user.registered' => [
        \App\Modules\LoggerModule::class,      // Логгер слушает регистрацию
        \App\Modules\EmailModule::class,       // Email слушает регистрацию
        \App\Modules\AnalyticsModule::class,   // Аналитика слушает регистрацию
    ],
    'order.paid' => [
        \App\Modules\LoggerModule::class,      // Логгер слушает оплату
        \App\Modules\EmailModule::class,       // Email слушает оплату
        // Аналитика НЕ слушает оплату (пример гибкости)
    ],
    'system.error' => [
        \App\Modules\LoggerModule::class,      // Только логгер слушает ошибки
    ]
];