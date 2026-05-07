<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 20px; }
  h1 { font-size: 20px; font-weight: bold; color: #2d2419; }
  h2 { font-size: 14px; font-weight: bold; color: #2d2419; margin-bottom: 8px; }
  .header { border-bottom: 2px solid #8b6f47; padding-bottom: 12px; margin-bottom: 16px; }
  .meta { font-size: 11px; color: #666; margin-top: 4px; }
  .stats-grid { display: flex; gap: 12px; margin-bottom: 20px; }
  .stat-box { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; background: #faf8f5; }
  .stat-label { font-size: 10px; color: #6b7280; }
  .stat-value { font-size: 16px; font-weight: bold; color: #2d2419; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  th { background: #2d2419; color: #fff; padding: 7px 10px; text-align: left; font-size: 11px; }
  td { padding: 6px 10px; border-bottom: 1px solid #f0ebe3; font-size: 11px; }
  tr:nth-child(even) td { background: #faf8f5; }
  .text-right { text-align: right; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: bold; }
  .badge-green { background: #d1fae5; color: #065f46; }
  .badge-red { background: #fee2e2; color: #991b1b; }
  .section { margin-bottom: 24px; }
  .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 10px; font-size: 10px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
  <div class="header">
    <h1>COFFEE POS — Laporan Penjualan</h1>
    <p class="meta">Periode: {{ $from }} s/d {{ $to }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
  </div>

  <div class="stats-grid">
    <div class="stat-box">
      <div class="stat-label">Total Penjualan</div>
      <div class="stat-value">Rp {{ number_format($report['total_sales'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Total Transaksi</div>
      <div class="stat-value">{{ $report['total_orders'] }}</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Biaya Produk</div>
      <div class="stat-value">Rp {{ number_format($report['product_cost'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Gross Profit</div>
      <div class="stat-value" style="color: {{ $report['gross_profit'] >= 0 ? '#065f46' : '#991b1b' }}">
        Rp {{ number_format($report['gross_profit'], 0, ',', '.') }}
      </div>
    </div>
  </div>

  @if(!empty($report['net_profit']))
  <div class="stats-grid">
    <div class="stat-box">
      <div class="stat-label">Total Pengeluaran</div>
      <div class="stat-value">Rp {{ number_format($report['total_expenses'] ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Net Profit</div>
      <div class="stat-value" style="color: {{ $report['net_profit'] >= 0 ? '#065f46' : '#991b1b' }}">
        Rp {{ number_format($report['net_profit'], 0, ',', '.') }}
      </div>
    </div>
    <div class="stat-box"></div>
    <div class="stat-box"></div>
  </div>
  @endif

  @if(!empty($bestSellers))
  <div class="section">
    <h2>Produk Terlaris</h2>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Produk</th>
          <th class="text-right">Qty Terjual</th>
          <th class="text-right">Revenue</th>
        </tr>
      </thead>
      <tbody>
        @foreach($bestSellers as $i => $item)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $item->product_name }}</td>
          <td class="text-right">{{ $item->total_qty }}</td>
          <td class="text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  @if(!empty($salesByDay))
  <div class="section">
    <h2>Penjualan per Hari</h2>
    <table>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th class="text-right">Jumlah Order</th>
          <th class="text-right">Total Penjualan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($salesByDay as $day)
        <tr>
          <td>{{ $day->date }}</td>
          <td class="text-right">{{ $day->total_orders }}</td>
          <td class="text-right">Rp {{ number_format($day->total_sales, 0, ',', '.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  <div class="footer">Coffee POS &copy; {{ date('Y') }} &nbsp;|&nbsp; Laporan ini digenerate otomatis oleh sistem</div>
</body>
</html>
