<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Expenses/Index');
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        Expense::create(array_merge($request->validated(), [
            'created_by' => $request->user()->id,
        ]));

        return back()->with('success', 'Pengeluaran berhasil disimpan.');
    }
}
