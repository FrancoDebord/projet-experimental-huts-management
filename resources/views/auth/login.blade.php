@extends('layouts.auth')
@section('title', 'Connexion — AIRID Huts Manager')
@section('subtitle', 'Connectez-vous à votre compte')

@section('content')
@if($errors->any())
<div class="alert alert-danger py-2 mb-3">
  <i class="fa-solid fa-circle-xmark me-2"></i>{{ $errors->first() }}
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
      @error('email')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
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
    <a href="{{ route('password.request') }}" class="small text-muted">
      <i class="fa-solid fa-key me-1"></i>Mot de passe oublié ?
    </a>
  </div>

  <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
    <i class="fa-solid fa-right-to-bracket me-2"></i>Se connecter
  </button>
</form>
@endsection

@push('scripts')
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
</script>
@endpush
