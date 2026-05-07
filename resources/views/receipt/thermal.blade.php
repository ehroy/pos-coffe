<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'Courier New', Courier, monospace;
    font-size: 12px;
    width: 80mm;
    padding: 4mm;
    color: #000;
    background: #fff;
  }
  .center { text-align: center; }
  .right { text-align: right; }
  .bold { font-weight: bold; }
  .divider { border-top: 1px dashed #000; margin: 4px 0; }
  .divider-solid { border-top: 1px solid #000; margin: 4px 0; }
  .row { display: flex; justify-content: space-between; margin: 2px 0; }
  .row .label { flex: 1; }
  .row .value { text-align: right; min-width: 80px; }
  .item-row { margin: 3px 0; }
  .item-name { font-weight: bold; }
  .item-detail { display: flex; justify-content: space-between; padding-left: 8px; }
  h1 { font-size: 16px; font-weight: bold; }
  h2 { font-size: 13px; font-weight: bold; }
  .footer { margin-top: 8px; font-size: 11px; }
  @media print {
    body { width: 80mm; }
    @page { margin: 0; size: 80mm auto; }
  }
</style>
</head>
<body>
  <div class="center">
    <h1>COFFEE POS</h1>
    <p>Jl. Kopi Nikmat No. 1</p>
    <p>Telp: (021) 123-4567</p>
  </div>

  <div class="divider-solid"></div>

  <div class="row">
    <span class="label">No. Order</span>
    <span class="value bold">{{ $order->order_number }}</span>
  </div>
  <div class="row">
    <span class="label">Tanggal</span>
    <span class="value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
  </div>
  @if($order->table)
  <div class="row">
    <span class="label">Meja</span>
    <span class="value">{{ $order->table->name }}</span>
  </div>
  @endif
  @if($order->customer_name)
  <div class="row">
    <span class="label">Customer</span>
    <span class="value">{{ $order->customer_name }}</span>
  </div>
  @endif
  @if($order->creator)
  <div class="row">
    <span class="label">Kasir</span>
    <span class="value">{{ $order->creator->name }}</span>
  </div>
  @endif

  <div class="divider"></div>

  @foreach($order->items as $item)
  <div class="item-row">
    <div class="item-name">{{ $item->product_name }}{{ $item->variant_name ? ' ('.$item->variant_name.')' : '' }}</div>
    <div class="item-detail">
      <span>{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
      <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
    </div>
    @if($item->note)
    <div style="padding-left:8px; font-size:11px; color:#555;">* {{ $item->note }}</div>
    @endif
  </div>
  @endforeach

  <div class="divider"></div>

  <div class="row">
    <span class="label">Subtotal</span>
    <span class="value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
  </div>
  @if($order->discount > 0)
  <div class="row">
    <span class="label">Diskon</span>
    <span class="value">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
  </div>
  @endif
  <div class="row">
    <span class="label">Pajak</span>
    <span class="value">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
  </div>
  @if(($order->service_charge ?? 0) > 0)
  <div class="row">
    <span class="label">Service Charge</span>
    <span class="value">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
  </div>
  @endif

  <div class="divider-solid"></div>

  <div class="row bold" style="font-size:14px;">
    <span class="label">TOTAL</span>
    <span class="value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
  </div>

  @if($order->payment)
  <div class="divider"></div>
  <div class="row">
    <span class="label">Metode</span>
    <span class="value">{{ strtoupper($order->payment->method) }}</span>
  </div>
  @if($order->payment->method === 'cash')
  <div class="row">
    <span class="label">Bayar</span>
    <span class="value">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</span>
  </div>
  <div class="row bold">
    <span class="label">Kembali</span>
    <span class="value">Rp {{ number_format($order->payment->change_amount, 0, ',', '.') }}</span>
  </div>
  @endif
  @endif

  <div class="divider-solid"></div>

  <div class="center footer">
    <p>Terima kasih atas kunjungan Anda!</p>
    <p>Selamat menikmati</p>
    <p style="margin-top:4px; font-size:10px;">{{ $order->order_number }} · {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
  </div>
</body>
</html>
