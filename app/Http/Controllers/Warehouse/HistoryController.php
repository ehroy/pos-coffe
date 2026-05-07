<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function index(): Response
    {
        $movements = StockMovement::with(['ingredient', 'creator'])
            ->latest()
            ->paginate(30);

        return Inertia::render('Warehouse/History', [
            'movements' => $movements,
        ]);
    }
}
