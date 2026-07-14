<?php

namespace App\Observers;

use App\Models\SalesOrder;
use App\Models\User;
use App\Notifications\SalesOrderCreated;
use Illuminate\Support\Facades\Notification;

class SalesOrderObserver
{
    public function created(SalesOrder $order): void
    {
        $admins = User::where('role', 'admin')->get();

        Notification::send($admins, new SalesOrderCreated($order));
    }
}
