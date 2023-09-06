<?php

namespace Triverla\OrderNotification;

use DateTime;
use Triverla\OrderNotification\Events\OrderStatusUpdated;

class OrderNotification
{
    public function __construct(string $orderUuid, string $status, DateTime $timestamp)
    {
        event(new OrderStatusUpdated($orderUuid, $status, $timestamp));
    }
}
