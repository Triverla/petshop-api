<?php

namespace Triverla\OrderNotification\Events;

use DateTime;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated
{
    use Dispatchable, SerializesModels;

    public string $orderUuid;
    public string $status;
    public DateTime $timestamp;

    public function __construct(string $orderUuid, string $status, DateTime $timestamp)
    {
        $this->orderUuid = $orderUuid;
        $this->status = $status;
        $this->timestamp = $timestamp;
    }
}
