<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — AIRID Huts Manager</title>
<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
<link rel="icon" href="/storage/assets/logo/airid.jpeg" type="image/jpeg">
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
    <div class="text-center mb-4">
      <img src="/storage/assets/logo/airid.jpeg" alt="AIRID" class="login-logo">
      <h4 class="mt-2">Cases Expérimentales</h4>
      <p class="subtitle">Connectez-vous à votre compte</p>
    </div>

    @if($errors->any())
      <div class="alert alert-danger py-2">
        <i class="fa-solid fa-circle-xmark me-1"></i>
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-semibold small">Adresse email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-envelope text-muted"></i></span>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold small">Mot de passe</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-lock text-muted"></i></span>
          <input type="password" name="password" id="passwordInput"
                 class="form-control" placeholder="••••••••" required>
          <button type="button" class="btn btn-outline-secondary" id="togglePwd">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>

      <div class="mb-4 d-flex align-items-center justify-content-between">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember">
          <label class="form-check-label small" for="remember">Se souvenir de moi</label>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
        <i class="fa-solid fa-right-to-bracket me-2"></i>Se connecter
      </button>
    </form>

    <div class="text-center mt-4">
      <small class="text-muted">
        <img src="/storage/assets/logo/airid.jpeg" alt="" height="16" class="me-1">
        African Institute for Research in Infectious Diseases
      </small>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePwd')?.addEventListener('click', function() {
  const input = document.getElementById('passwordInput');
  const icon  = this.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
});
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(() => {});
}
</script>
</body>
</html>
