<?php

namespace App\Providers;

use App\Events\LowStockDetected;
use App\Events\OrderCreated;
use App\Events\PaymentCompleted;
use App\Listeners\NotifyLowStock;
use App\Listeners\SendOrderToKitchen;
use App\Listeners\UpdateCashSession;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Event::listen(OrderCreated::class, SendOrderToKitchen::class);
        Event::listen(PaymentCompleted::class, UpdateCashSession::class);
        Event::listen(LowStockDetected::class, NotifyLowStock::class);
    }
}
