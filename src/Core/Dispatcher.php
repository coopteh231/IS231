<?php
namespace App\Core;

class Dispatcher
{
    // Хранилище подписчиков: [eventName => [callback1, callback2]]
    private array $listeners = [];

    /**
     * Подписка на событие
     */
    public function subscribe(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * Генерация (диспетчеризация) события
     */
    public function dispatch(Event $event): void
    {
        $eventName = $event->getName();

        if (!isset($this->listeners[$eventName])) {
            return; // Никто не слушает это событие
        }

        echo "\n[ЯДРО] Событие '{$eventName}' запущено. Обработчиков: " . count($this->listeners[$eventName]) . "\n";

        foreach ($this->listeners[$eventName] as $listener) {
            // Вызываем метод handle у модуля, передавая событие
            call_user_func($listener, $event);
        }
    }
}
