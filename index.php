<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Event;
use App\Core\Dispatcher;

use App\Modules\LoggerModule;
use App\Modules\EmailModule;
use App\Modules\AnalyticsModule;

// 1. Инициализация ядра
$dispatcher = new Dispatcher();

// 2. Загрузка конфигурации
$config = require __DIR__ .'/Config/config.php';

// 3. Динамическая регистрация подписчиков на основе настроек
foreach ($config as $eventName => $subscriberClasses) {
    foreach ($subscriberClasses as $class) {
        // Создаем экземпляр модуля
        $subscriber = new $class();
        
        // Подписываем метод handle этого модуля на событие
        $dispatcher->subscribe($eventName, [$subscriber, 'handle']);
    }
}

// Тестовые сценарии исполнения
echo "=== СИСТЕМА ЗАПУЩЕНА ===\n";

// --- Сценарий 1: Регистрация пользователя ---
echo "\n--- Сценарий: Регистрация пользователя ---\n";
$registerEvent = new Event('user.registered', [
    'user_id' => 101,
    'email' => 'ivan@example.com',
    'name' => 'Ivan'
]);
$dispatcher->dispatch($registerEvent);

// --- Сценарий 2: Оплата заказа ---
echo "\n--- Сценарий: Оплата заказа ---\n";
$payEvent = new Event('order.paid', [
    'order_id' => 555,
    'email' => 'ivan@example.com',
    'amount' => 1500
]);
$dispatcher->dispatch($payEvent);

// --- Сценарий 3: Ошибка системы ---
echo "\n--- Сценарий: Ошибка системы ---\n";
$errorEvent = new Event('system.error', [
    'message' => 'Database connection failed',
    'code' => 500
]);
$dispatcher->dispatch($errorEvent);

echo "\n=== РАБОТА ЗАВЕРШЕНА ===\n";