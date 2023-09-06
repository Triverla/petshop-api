<?php

namespace Triverla\OrderNotification\Tests;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\MockObject\Exception;
use GuzzleHttp\Client;
use Triverla\OrderNotification\Events\OrderStatusUpdated;
use Triverla\OrderNotification\Listeners\SendOrderStatusNotification;
use Triverla\OrderNotification\Services\HttpService;
use Triverla\OrderNotification\TeamsMessage;


class OrderStatusUpdatedTest extends TestCase
{
    public function testOrderStatusUpdatedEvent()
    {
        // Fire the event
        $event = new OrderStatusUpdated('test123', 'processing', Carbon::parse('2023-09-03 12:00:00'));

        event($event);

        // Assert that the event was fired with the correct data
        $this->assertEquals('test123', $event->orderUuid);
        $this->assertEquals('processing', $event->status);
        $this->assertEquals(Carbon::parse('2023-09-03 12:00:00'), $event->timestamp);
    }

    /** @test */
    public function testCanSendANotification()
    {
        $mockClient = $this->getMockBuilder(Client::class)
            ->onlyMethods(['post'])
            ->getMock();
        $mockClient
            ->method('post')
            ->with(
                'https://webhook.site/2b63b8dc-ddf4-4d74-8d53-17f0df0911f1',
                [
                    '@type' => 'MessageCard',
                    '@context' => 'https://schema.org/extensions',
                    'summary' => 'Order Status Update',
                    'themeColor' => '#1976D2',
                    'title' => 'Order #1234 updated',
                    'text' => 'Order status changed to processing',
                ]
            )
            ->willReturn(new Response());

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function testHandleMethodSendsNotification()
    {
        $http = Http::fake();

        $event = new OrderStatusUpdated(
            'test123',
            'processing',
            Carbon::parse('2023-08-30 10:00:00')
        );

        Config::set('order-notification.webhook_url', 'https://webhook.site/2b63b8dc-ddf4-4d74-8d53-17f0df0911f1');

        $notification = new SendOrderStatusNotification();

        $notification->handle($event);

        [$request1] = $http->recorded()->get(0);

        $this->assertEquals((new TeamsMessage())->getMessage($event), $request1->data());
    }
}
