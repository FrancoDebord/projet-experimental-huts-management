@extends('layouts.app')
@section('title', 'Nouveau dormeur')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header"><i class="fa-solid fa-person me-2 text-primary"></i>Ajouter un dormeur</div>
      <div class="card-body">
        <form method="POST" action="{{ route('sleepers.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Code / Identifiant <span class="text-danger">*</span></label>
              <input type="text" name="code" class="form-control" value="{{ old('code') }}" required placeholder="Ex: D01, SLP-A…">
              <small class="text-muted">Identifiant court unique</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Genre</label>
              <select name="gender" class="form-select">
                <option value="">— Non spécifié —</option>
                <option value="M" {{ old('gender')==='M'?'selected':'' }}>Homme</option>
                <option value="F" {{ old('gender')==='F'?'selected':'' }}>Femme</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Site assigné</label>
              <select name="site_id" class="form-select">
                <option value="">Pool général</option>
                @foreach($sites as $s)
                <option value="{{ $s->id }}" {{ old('site_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="active" value="1" id="activeCheck" checked>
                <label class="form-check-label" for="activeCheck">Dormeur actif</label>
              </div>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer</button>
            <a href="{{ route('sleepers.index') }}" class="btn btn-outline-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
