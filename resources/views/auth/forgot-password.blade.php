@extends('layouts.auth')
@section('title', 'Mot de passe oublié — AIRID Huts Manager')
@section('subtitle', 'Réinitialisation du mot de passe')

@section('content')
<p class="text-muted small text-center mb-4">
  Saisissez votre adresse email. Vous recevrez un lien pour créer un nouveau mot de passe.
</p>

@if($errors->any())
<div class="alert alert-danger py-2 mb-3">
  <i class="fa-solid fa-circle-xmark me-2"></i>{{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
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

  <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
    <i class="fa-solid fa-paper-plane me-2"></i>Envoyer le lien de réinitialisation
  </button>
</form>

<div class="text-center mt-3">
  <a href="{{ route('login') }}" class="small">
    <i class="fa-solid fa-arrow-left me-1"></i>Retour à la connexion
  </a>
</div>
@endsection
