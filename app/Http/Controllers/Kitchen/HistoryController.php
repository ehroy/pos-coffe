<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function index(): Response
    {
        $date = request()->input('date');
        $search = request()->input('search');

        $query = Order::with(['items', 'table', 'payment'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest('updated_at');

        if ($date) {
            $query->whereDate('created_at', Carbon::parse($date));
        }

        if ($search) {
            $query->where('order_number', 'like', "%{$search}%");
        }

        return Inertia::render('Kitchen/History', [
            'orders'  => $query->paginate(20)->withQueryString(),
            'filters' => [
                'date'   => $date ?? '',
                'search' => $search ?? '',
            ],
        ]);
    }
}
