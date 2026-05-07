<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; background: #fff; }
  .page {
    width: 148mm;
    min-height: 210mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20mm;
    text-align: center;
  }
  .brand { font-size: 22px; font-weight: bold; color: #2d2419; letter-spacing: 2px; margin-bottom: 4px; }
  .tagline { font-size: 11px; color: #8b6f47; margin-bottom: 20px; }
  .table-name { font-size: 28px; font-weight: bold; color: #2d2419; margin-bottom: 4px; }
  .table-code { font-size: 13px; color: #6b7280; margin-bottom: 20px; }
  .qr-box { border: 3px solid #2d2419; border-radius: 12px; padding: 16px; background: #fff; display: inline-block; }
  .qr-box img { width: 200px; height: 200px; }
  .instruction { margin-top: 20px; font-size: 12px; color: #4b5563; line-height: 1.6; }
  .url { margin-top: 12px; font-size: 9px; color: #9ca3af; word-break: break-all; }
  .divider { width: 60px; height: 3px; background: #8b6f47; border-radius: 2px; margin: 16px auto; }
</style>
</head>
<body>
  <div class="page">
    <div class="brand">COFFEE POS</div>
    <div class="tagline">Scan untuk memesan</div>
    <div class="divider"></div>
    <div class="table-name">{{ $table->name }}</div>
    <div class="table-code">Kode: {{ $table->code }}</div>
    <div class="qr-box">
      <img src="{{ $qrBase64 }}" alt="QR Code {{ $table->name }}" />
    </div>
    <div class="instruction">
      Scan QR code di atas dengan kamera HP Anda<br>
      untuk melihat menu dan memesan langsung dari meja ini.
    </div>
    <div class="url">{{ $url }}</div>
  </div>
</body>
</html>
