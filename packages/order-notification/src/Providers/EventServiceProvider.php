<?php

namespace Triverla\OrderNotification\Providers;

use Illuminate\Support\ServiceProvider;
use Triverla\OrderNotification\Events\OrderStatusUpdated;
use Triverla\OrderNotification\Listeners\SendOrderStatusNotification;

class EventServiceProvider extends ServiceProvider
{
    protected array $listen = [
        OrderStatusUpdated::class => [
            [SendOrderStatusNotification::class, 'handle'],
        ]
    ];

    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
