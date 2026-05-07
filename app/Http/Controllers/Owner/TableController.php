<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreTableRequest;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Owner/Tables/Index', [
            'tables' => Table::orderBy('code')
                ->get()
                ->map(fn (Table $table) => [
                    'id' => $table->id,
                    'code' => $table->code,
                    'name' => $table->name,
                    'qr_token' => $table->qr_token,
                    'qr_url' => $table->qr_url,
                    'is_active' => $table->is_active,
                ]),
        ]);
    }

    public function store(StoreTableRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $code = $data['code'] ?? 'T' . str_pad((string) (Table::count() + 1), 3, '0', STR_PAD_LEFT);

        Table::create([
            'code'      => $code,
            'name'      => $data['name'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return back()->with('success', 'Meja berhasil ditambahkan.');
    }

    public function toggleActive(Table $table): RedirectResponse
    {
        $table->update(['is_active' => ! $table->is_active]);

        return back()->with('success', 'Status meja diperbarui.');
    }
}
