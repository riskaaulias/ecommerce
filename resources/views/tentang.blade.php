<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    {{-- ↑ Encoding karakter --}}

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {{-- ↑ Responsive untuk mobile --}}

    <title>Tentang Kami</title>

    <style>
      body {
        font-family: system-ui, -apple-system, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
      }
      h1 {
        color: #fcff3eff;
      }
    </style>
  </head>
  <body>
    <h1>Tentang Toko Online</h1>
    <p>Selamat datang di toko online kami.</p>
    <p>Dibuat dengan 😘💛💛💛💛💛 menggunakan Laravel.</p>
    <p>Waktu saat ini: {{ now()->format('d M Y, H:i:s') }}</p>
   

    <a href="/">← Kembali ke Home</a>
    {{-- ↑ Link biasa ke halaman utama --}}
  </body>
</html>