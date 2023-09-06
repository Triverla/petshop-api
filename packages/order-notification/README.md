# Notification

A simple laravel package that provides an OrderStatusUpdated event and a listener that sends a message to a Microsoft Teams webhook.

## Installation

This package is not an actual published package.

Once package is installed, Laravel will auto-detect this package and will register the provided Service Provider.

The Service Provider binds by default a simple Microsoft Teams `MessageCard` as the payload to the webhook.

## Configuration

Set `order-notification.webhook_url` (**required**) to your Microsoft Teams webhook URL in `order-notification` config file or via .env

```php
ORDERS_WEBHOOK_URL=
```

## Testing

```bash
composer test
```
