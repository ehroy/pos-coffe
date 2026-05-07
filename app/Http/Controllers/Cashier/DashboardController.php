<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();
        $user  = request()->user();

        $todaySales = Payment::query()
            ->whereDate('paid_at', $today)
            ->where('status', 'paid')
            ->sum('amount');

        $todayOrders = Order::query()
            ->whereDate('created_at', $today)
            ->count();

        $pendingOrders = Order::query()
            ->whereDate('created_at', $today)
            ->where('payment_status', 'unpaid')
            ->count();

        $tableOrdersPending = Order::query()
            ->whereDate('created_at', $today)
            ->where('source', 'qr_table')
            ->where('status', 'pending')
            ->count();

        $tableOrdersAccepted = Order::query()
            ->whereDate('created_at', $today)
            ->where('source', 'qr_table')
            ->where('status', 'accepted')
            ->count();

        $qrisPendingOrders = Order::query()
            ->whereDate('created_at', $today)
            ->where('source', 'qr_table')
            ->where('payment_method', 'qris')
            ->where('payment_status', 'unpaid')
            ->where('status', 'pending')
            ->count();

        $activeSession = CashSession::open()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return Inertia::render('Cashier/Dashboard', [
            'stats' => [
                'today_sales'           => (float) $todaySales,
                'today_orders'          => $todayOrders,
                'pending_orders'        => $pendingOrders,
                'table_orders_pending'  => $tableOrdersPending,
                'table_orders_accepted' => $tableOrdersAccepted,
                'qris_pending_orders' => $qrisPendingOrders,
                'has_active_session'    => $activeSession !== null,
                'session_opening_cash'  => $activeSession ? (float) $activeSession->opening_cash : null,
                'session_opened_at'     => $activeSession?->opened_at?->toISOString(),
            ],
            'recentOrders' => Order::with(['items', 'payment'])
                ->whereDate('created_at', $today)
                ->latest('created_at')
                ->limit(5)
                ->get(),
            'pendingTableOrders' => Order::with(['items', 'table'])
                ->whereDate('created_at', $today)
                ->where('source', 'qr_table')
                ->where('status', 'pending')
                ->latest('created_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
