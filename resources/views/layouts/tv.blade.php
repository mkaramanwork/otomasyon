{{-- resources/views/layouts/tv.blade.php --}}
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>@yield('title')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #000; color: #fff; font-size: 1.2rem; }
    table { background-color: #111; }
    th, td { vertical-align: middle; }
  </style>
</head>
<body>
  @yield('content')

  <!-- ✅ JavaScript ile otomatik yenileme -->
  <script>
    setInterval(function() {
      window.location.reload();
    }, 30000); // 30 saniyede bir yenile
  </script>
</body>
</html>