<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Owner/Tables/Index', [
            'tables' => Table::orderBy('code')->get(),
        ]);
    }
}
