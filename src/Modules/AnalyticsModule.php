<?php
namespace App\Modules;

use App\Core\Event;

class AnalyticsModule
{
    public function handle(Event $event): void
    {
        echo "[ANALYTICS] Трекинг действия: {$event->getName()} (User ID: " . ($event->getData()['user_id'] ?? 'N/A') . ")\n";
    }
}
