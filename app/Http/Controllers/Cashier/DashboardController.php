<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Cashier/Dashboard', [
            'stats' => [
                'today_sales' => 0, // Placeholder untuk Phase 4
                'today_orders' => 0, // Placeholder untuk Phase 4
                'pending_orders' => 0, // Placeholder untuk Phase 4
                'cash_session_status' => null, // Placeholder untuk Phase 7
            ],
        ]);
    }
}
