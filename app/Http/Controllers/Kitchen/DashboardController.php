<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();

        return Inertia::render('Kitchen/Dashboard', [
            'stats' => [
                'pending_orders' => Order::query()->where('status', 'accepted')->count(),
                'processing_orders' => Order::query()->where('status', 'processing')->count(),
                'completed_today' => Order::query()->whereDate('created_at', $today)->where('status', 'completed')->count(),
            ],
            'orders' => Order::with(['items', 'table'])
                ->whereIn('status', ['accepted', 'processing', 'ready'])
                ->latest('created_at')
                ->limit(6)
                ->get(),
        ]);
    }
}
