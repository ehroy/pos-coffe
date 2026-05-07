<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; background: #fff; }
  .header { text-align: center; padding: 16px; border-bottom: 2px solid #2d2419; margin-bottom: 16px; }
  .header h1 { font-size: 20px; font-weight: bold; color: #2d2419; }
  .header p { font-size: 11px; color: #6b7280; margin-top: 4px; }
  .grid { display: flex; flex-wrap: wrap; gap: 12px; padding: 0 12px; }
  .card {
    width: calc(25% - 9px);
    border: 2px solid #2d2419;
    border-radius: 10px;
    padding: 12px;
    text-align: center;
    background: #fff;
  }
  .card .table-name { font-size: 13px; font-weight: bold; color: #2d2419; margin-bottom: 2px; }
  .card .table-code { font-size: 10px; color: #6b7280; margin-bottom: 8px; }
  .card img { width: 100px; height: 100px; }
  .card .url { font-size: 7px; color: #9ca3af; margin-top: 6px; word-break: break-all; }
</style>
</head>
<body>
  <div class="header">
    <h1>COFFEE POS — QR Code Semua Meja</h1>
    <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
  </div>
  <div class="grid">
    @foreach($tables as $item)
    <div class="card">
      <div class="table-name">{{ $item['table']->name }}</div>
      <div class="table-code">{{ $item['table']->code }}</div>
      <img src="{{ $item['qrBase64'] }}" alt="QR {{ $item['table']->name }}" />
      <div class="url">{{ $item['url'] }}</div>
    </div>
    @endforeach
  </div>
</body>
</html>
