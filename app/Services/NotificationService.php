<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Notifications\SystemAlertNotification;

class NotificationService
{
    public static function notifyAdmins(string $title, string $message, ?string $url = null, string $type = 'info', ?int $orderId = null): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new SystemAlertNotification($title, $message, $url, $type, $orderId));
        }
    }

    public static function notifyCustomer(Order $order, string $title, string $message, ?string $url = null, string $type = 'info'): void
    {
        if ($order->customer && $order->customer->user) {
            $order->customer->user->notify(new SystemAlertNotification($title, $message, $url, $type, $order->id));
        }
    }

    public static function notifyDriver(User $driverUser, string $title, string $message, ?string $url = null, string $type = 'info', ?int $orderId = null): void
    {
        $driverUser->notify(new SystemAlertNotification($title, $message, $url, $type, $orderId));
    }
}

