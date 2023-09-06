<?php

namespace Triverla\OrderNotification\Listeners;

use Illuminate\Support\Facades\Http;
use Triverla\OrderNotification\Events\OrderStatusUpdated;
use Triverla\OrderNotification\TeamsMessage;

class SendOrderStatusNotification
{
    private string $messageClass;

    public function __construct(?string $messageClass = null)
    {
        $this->messageClass = $messageClass ?? TeamsMessage::class;
    }

    public function handle(OrderStatusUpdated $event): void
    {
        $message = new $this->messageClass($event);

        Http::post(config('order-notification.webhook_url'), $message->getMessage($event));
    }
}
