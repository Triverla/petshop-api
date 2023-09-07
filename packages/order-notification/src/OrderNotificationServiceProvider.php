<?php

namespace Triverla\OrderNotification;

use Illuminate\Support\ServiceProvider;
use Triverla\OrderNotification\Listeners\SendOrderStatusNotification;
use Triverla\OrderNotification\Providers\EventServiceProvider;

class OrderNotificationServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        if (function_exists('config_path') && $this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/order-notification.php' => config_path('order-notification.php'),
            ], 'config');
        }

        $this->app->singleton(SendOrderStatusNotification::class, function ($app) {
            return new SendOrderStatusNotification();
        });
    }


    /**
     * Register the application services.
     */
    public function register(): void
    {
        //dd('Registering EventServiceProvider');
        $this->app->register(EventServiceProvider::class);
    }
}
