<?php
namespace App\Modules;

use App\Core\Event;

class LoggerModule
{
    public function handle(Event $event): void
    {
        $data = json_encode($event->getData());
        echo "[LOGGER] Запись в лог: Событие '{$event->getName()}' с данными: {$data}\n";
    }
}
