<?php

namespace Triverla\OrderNotification\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Triverla\OrderNotification\Events\OrderStatusUpdated;
use Triverla\OrderNotification\Listeners\SendOrderStatusNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderStatusUpdated::class => [
            [SendOrderStatusNotification::class, 'handle'],
        ]
    ];
}
