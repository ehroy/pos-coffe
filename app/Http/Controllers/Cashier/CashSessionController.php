<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\StoreCashSessionRequest;
use App\Models\CashSession;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CashSessionController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function index(): Response
    {
        $session = CashSession::open()
            ->where('user_id', request()->user()->id)
            ->latest()
            ->first();

        $recentSessions = CashSession::where('user_id', request()->user()->id)
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Cashier/CashSession', [
            'session' => $session,
            'recentSessions' => $recentSessions,
        ]);
    }

    public function open(StoreCashSessionRequest $request): RedirectResponse
    {
        try {
            $this->paymentService->openSession(
                $request->user(),
                (float) $request->validated()['opening_cash']
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Sesi kas berhasil dibuka.');
    }

    public function close(Request $request): RedirectResponse
    {
        $request->validate([
            'closing_cash' => ['required', 'numeric', 'min:0'],
        ]);

        $session = CashSession::open()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->first();

        if (! $session) {
            return back()->with('error', 'Tidak ada sesi kas yang aktif.');
        }

        try {
            $this->paymentService->closeSession($session, (float) $request->closing_cash);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Sesi kas berhasil ditutup.');
    }
}
