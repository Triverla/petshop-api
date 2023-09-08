<?php

namespace App\Observers;

use App\Models\Order;
use Carbon\Carbon;
use Triverla\OrderNotification\Events\OrderStatusUpdated;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('order_status_id')) {
            event(
                new OrderStatusUpdated(
                    $order->uuid,
                    $order->orderStatus->title,
                    Carbon::parse($order->created_at)->toDateTime()
                )
            );
        }
    }
}
