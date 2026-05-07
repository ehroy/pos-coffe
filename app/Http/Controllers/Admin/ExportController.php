<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class ExportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function salesPdf(Request $request): Response
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
            'type' => ['nullable', 'in:daily,weekly,monthly,custom'],
        ]);

        [$from, $to] = $this->resolveDateRange($request);

        $report      = $this->reportService->profitReport($from, $to);
        $bestSellers = $this->reportService->bestSellers($from, $to);
        $salesByDay  = $this->reportService->salesByDay($from, $to);

        $pdf = Pdf::loadView('pdf.report-sales', [
            'from'        => $from->format('d/m/Y'),
            'to'          => $to->format('d/m/Y'),
            'report'      => $report,
            'bestSellers' => $bestSellers,
            'salesByDay'  => $salesByDay,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-penjualan-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function salesCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);

        $salesByDay  = $this->reportService->salesByDay($from, $to);
        $bestSellers = $this->reportService->bestSellers($from, $to);
        $report      = $this->reportService->profitReport($from, $to);

        $filename = 'laporan-penjualan-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($salesByDay, $bestSellers, $report, $from, $to) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['LAPORAN PENJUALAN COFFEE POS']);
            fputcsv($handle, ['Periode', $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y')]);
            fputcsv($handle, ['Dicetak', now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);

            fputcsv($handle, ['RINGKASAN']);
            fputcsv($handle, ['Total Penjualan', 'Rp ' . number_format($report['total_sales'], 0, ',', '.')]);
            fputcsv($handle, ['Total Transaksi', $report['total_orders']]);
            fputcsv($handle, ['Biaya Produk', 'Rp ' . number_format($report['product_cost'], 0, ',', '.')]);
            fputcsv($handle, ['Gross Profit', 'Rp ' . number_format($report['gross_profit'], 0, ',', '.')]);
            fputcsv($handle, ['Total Pengeluaran', 'Rp ' . number_format($report['total_expenses'] ?? 0, 0, ',', '.')]);
            fputcsv($handle, ['Net Profit', 'Rp ' . number_format($report['net_profit'] ?? 0, 0, ',', '.')]);
            fputcsv($handle, []);

            fputcsv($handle, ['PENJUALAN PER HARI']);
            fputcsv($handle, ['Tanggal', 'Jumlah Order', 'Total Penjualan']);
            foreach ($salesByDay as $day) {
                fputcsv($handle, [$day->date, $day->total_orders, $day->total_sales]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['PRODUK TERLARIS']);
            fputcsv($handle, ['#', 'Produk', 'Qty Terjual', 'Revenue']);
            foreach ($bestSellers as $i => $item) {
                fputcsv($handle, [$i + 1, $item->product_name, $item->total_qty, $item->total_revenue]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function expensesCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()));
        $to   = Carbon::parse($request->input('to', now()));

        $data     = $this->reportService->expenseReport($from, $to);
        $filename = 'pengeluaran-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($data, $from, $to) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['LAPORAN PENGELUARAN COFFEE POS']);
            fputcsv($handle, ['Periode', $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Total Pengeluaran', 'Rp ' . number_format($data['total'], 0, ',', '.')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Tanggal', 'Judul', 'Kategori', 'Jumlah', 'Catatan']);

            foreach ($data['items'] as $expense) {
                fputcsv($handle, [
                    $expense->expense_date,
                    $expense->title,
                    $expense->category,
                    $expense->amount,
                    $expense->note ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $type = $request->input('type', 'custom');
        $date = Carbon::parse($request->input('date', now()));

        return match ($type) {
            'daily'   => [$date->copy()->startOfDay(), $date->copy()->endOfDay()],
            'weekly'  => [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()],
            'monthly' => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
            default   => [
                Carbon::parse($request->input('from', now()->startOfMonth())),
                Carbon::parse($request->input('to', now())),
            ],
        };
    }
}
