<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Kitchen/Dashboard', [
            'stats' => [
                'pending_orders' => 0, // Placeholder untuk Phase 6
                'processing_orders' => 0, // Placeholder untuk Phase 6
                'completed_today' => 0, // Placeholder untuk Phase 6
            ],
        ]);
    }
}
