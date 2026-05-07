<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function sales(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['nullable', 'in:daily,weekly,monthly,custom'],
            'date' => ['nullable', 'date'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $type = $request->input('type', 'daily');
        $date = Carbon::parse($request->input('date', now()));

        $data = match ($type) {
            'weekly' => $this->reportService->weeklySales($date),
            'monthly' => $this->reportService->monthlySales($date),
            'custom' => $this->reportService->salesSummary(
                Carbon::parse($request->input('from', now()->startOfMonth())),
                Carbon::parse($request->input('to', now()))
            ),
            default => $this->reportService->dailySales($date),
        };

        $data['best_sellers'] = $this->reportService->bestSellers(
            Carbon::parse($data['from']),
            Carbon::parse($data['to'])
        );

        $data['sales_by_day'] = $this->reportService->salesByDay(
            Carbon::parse($data['from']),
            Carbon::parse($data['to'])
        );

        return response()->json($data);
    }

    public function profit(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()));
        $to = Carbon::parse($request->input('to', now()));

        return response()->json($this->reportService->profitReport($from, $to));
    }

    public function expenses(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()));
        $to = Carbon::parse($request->input('to', now()));

        return response()->json($this->reportService->expenseReport($from, $to));
    }
}
