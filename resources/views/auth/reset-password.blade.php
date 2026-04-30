@extends('layouts.auth')
@section('title', 'Nouveau mot de passe — AIRID Huts Manager')
@section('subtitle', 'Choisissez un nouveau mot de passe')

@section('content')
@if($errors->any())
<div class="alert alert-danger py-2 mb-3">
  <i class="fa-solid fa-circle-xmark me-2"></i>{{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.update') }}">
  @csrf
  <input type="hidden" name="token" value="{{ $token }}">

  <div class="mb-3">
    <label class="form-label fw-semibold small">Adresse email</label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa-solid fa-envelope text-muted"></i></span>
      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
             value="{{ old('email', $email ?? '') }}" placeholder="votre@email.com" required autofocus>
      @error('email')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label fw-semibold small">Nouveau mot de passe</label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa-solid fa-lock text-muted"></i></span>
      <input type="password" name="password" id="passwordNew"
             class="form-control @error('password') is-invalid @enderror"
             placeholder="Minimum 8 caractères" required>
      <button type="button" class="btn btn-outline-secondary" id="toggleNew">
        <i class="fa-solid fa-eye"></i>
      </button>
      @error('password')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div id="strengthBar" class="mt-1" style="height:4px;border-radius:4px;background:#e9ecef;transition:all .3s">
      <div id="strengthFill" style="height:100%;border-radius:4px;width:0;transition:all .3s"></div>
    </div>
    <small id="strengthLabel" class="text-muted"></small>
  </div>

  <div class="mb-4">
    <label class="form-label fw-semibold small">Confirmer le mot de passe</label>
    <div class="input-group">
      <span class="input-group-text"><i class="fa-solid fa-lock-open text-muted"></i></span>
      <input type="password" name="password_confirmation" id="passwordConfirm"
             class="form-control @error('password_confirmation') is-invalid @enderror"
             placeholder="Répétez le mot de passe" required>
      <button type="button" class="btn btn-outline-secondary" id="toggleConfirm">
        <i class="fa-solid fa-eye"></i>
      </button>
      @error('password_confirmation')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <small id="matchMsg" class="mt-1 d-block"></small>
  </div>

  <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" id="submitBtn">
    <i class="fa-solid fa-key me-2"></i>Réinitialiser le mot de passe
  </button>
</form>

<div class="text-center mt-3">
  <a href="{{ route('login') }}" class="small">
    <i class="fa-solid fa-arrow-left me-1"></i>Retour à la connexion
  </a>
</div>
@endsection

@push('scripts')
<script>
// Toggle visibility
function toggleVis(inputId, btnId) {
  document.getElementById(btnId).addEventListener('click', function() {
    const inp  = document.getElementById(inputId);
    const icon = this.querySelector('i');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
  });
}
toggleVis('passwordNew', 'toggleNew');
toggleVis('passwordConfirm', 'toggleConfirm');

// Password strength
const pwdInput   = document.getElementById('passwordNew');
const fill       = document.getElementById('strengthFill');
const label      = document.getElementById('strengthLabel');
const confirmInp = document.getElementById('passwordConfirm');
const matchMsg   = document.getElementById('matchMsg');

pwdInput.addEventListener('input', function() {
  const v = this.value;
  let score = 0;
  if (v.length >= 8)  score++;
  if (v.length >= 12) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^A-Za-z0-9]/.test(v)) score++;

  const levels = [
    { w: '0%',   color: '',        text: '' },
    { w: '25%',  color: '#dc3545', text: 'Très faible' },
    { w: '50%',  color: '#ffc107', text: 'Faible' },
    { w: '75%',  color: '#0d6efd', text: 'Correct' },
    { w: '100%', color: '#198754', text: 'Fort' },
    { w: '100%', color: '#146c43', text: 'Très fort' },
  ];
  const lvl = levels[Math.min(score, 5)];
  fill.style.width = lvl.w;
  fill.style.background = lvl.color;
  label.textContent = lvl.text;
  label.style.color = lvl.color;
  checkMatch();
});

confirmInp.addEventListener('input', checkMatch);

function checkMatch() {
  const p = pwdInput.value;
  const c = confirmInp.value;
  if (!c) { matchMsg.textContent = ''; return; }
  if (p === c) {
    matchMsg.innerHTML = '<i class="fa-solid fa-check text-success me-1"></i><span class="text-success">Les mots de passe correspondent.</span>';
  } else {
    matchMsg.innerHTML = '<i class="fa-solid fa-xmark text-danger me-1"></i><span class="text-danger">Les mots de passe ne correspondent pas.</span>';
  }
}
</script>
@endpush
