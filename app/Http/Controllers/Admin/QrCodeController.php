<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function show(Table $table): \Illuminate\Http\Response
    {
        $url = $table->qr_url;

        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($url);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function pdf(Table $table): Response
    {
        $url = $table->qr_url;

        $svgContent = QrCode::format('svg')
            ->size(250)
            ->margin(2)
            ->generate($url);

        $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);

        $pdf = Pdf::loadView('pdf.qr-table', [
            'table' => $table,
            'qrBase64' => $svgBase64,
            'url' => $url,
        ])->setPaper('a5', 'portrait');

        return $pdf->download("qr-{$table->code}.pdf");
    }

    public function bulkPdf(): Response
    {
        $tables = Table::active()->orderBy('code')->get();

        $tableData = $tables->map(function (Table $table) {
            $svgContent = QrCode::format('svg')
                ->size(200)
                ->margin(2)
                ->generate($table->qr_url);

            return [
                'table' => $table,
                'qrBase64' => 'data:image/svg+xml;base64,' . base64_encode($svgContent),
                'url' => $table->qr_url,
            ];
        });

        $pdf = Pdf::loadView('pdf.qr-bulk', ['tables' => $tableData])
            ->setPaper('a4', 'portrait');

        return $pdf->download('qr-all-tables.pdf');
    }
}
