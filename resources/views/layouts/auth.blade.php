<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'AIRID Huts Manager')</title>
<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
<link rel="apple-touch-icon" sizes="192x192" href="/storage/assets/logo/icon-192.png">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#CC0000">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="AIRID Huts">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="/css/airid.css" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">
  <div class="login-card">
    {{-- Logo + titre --}}
    <div class="text-center mb-4">
      <img src="/storage/assets/logo/airid.jpeg" alt="AIRID" class="login-logo">
      <h4 class="mt-2 fw-bold">Cases Expérimentales</h4>
      <p class="subtitle">@yield('subtitle', 'Connectez-vous à votre compte')</p>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
    <div class="alert alert-success py-2 mb-3">
      <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Contenu de la page --}}
    @yield('content')

    {{-- Pied de page --}}
    <div class="text-center mt-4 pt-3 border-top">
      <small class="text-muted">
        <img src="/storage/assets/logo/airid.jpeg" alt="" height="16" class="me-1" style="border-radius:2px">
        African Institute for Research in Infectious Diseases
      </small>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(() => {});
}
</script>
@stack('scripts')
</body>
</html>
