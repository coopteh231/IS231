<?php
namespace App\Modules;

use App\Core\Event;

class EmailModule
{
    public function handle(Event $event): void
    {
        $data = $event->getData();
        if (isset($data['email'])) {
            echo "[EMAIL] Отправка письма на {$data['email']} о событии '{$event->getName()}'\n";
        }
    }
}