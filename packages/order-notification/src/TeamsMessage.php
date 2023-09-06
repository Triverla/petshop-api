<?php

namespace Triverla\OrderNotification;

use Carbon\Carbon;
use Triverla\OrderNotification\Events\OrderStatusUpdated;

class TeamsMessage
{
    public function getMessage(OrderStatusUpdated $event): array
    {
        return [
            '@type' => 'MessageCard',
            '@context' => 'https://schema.org/extensions',
            'title' => 'Order Status Updated',
            'summary' => "Order {$event->orderUuid} status update",
            'sections' => [
                [
                    'facts' => [
                        [
                            'name' => 'Order:',
                            'value' => $event->orderUuid,
                        ],
                        [
                            'name' => 'Status:',
                            'value' => $event->status,
                        ],
                        [
                            'name' => 'Update:',
                            'value' => Carbon::parse($event->timestamp)->format('D M d Y H:i:s e (T)'),
                        ],
                    ],
                ],
            ],
        ];
    }

}
