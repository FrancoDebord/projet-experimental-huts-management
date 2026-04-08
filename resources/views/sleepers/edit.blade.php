@extends('layouts.app')
@section('title', 'Modifier dormeur — '.$sleeper->name)

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <span><i class="fa-solid fa-pen me-2 text-primary"></i>Modifier le dormeur</span>
        <a href="{{ route('sleepers.index') }}" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-arrow-left me-1"></i>Retour
        </a>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('sleepers.update', $sleeper) }}">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $sleeper->name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
              <input type="text" name="code" class="form-control" value="{{ old('code', $sleeper->code) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Genre</label>
              <select name="gender" class="form-select">
                <option value="">— Non spécifié —</option>
                <option value="M" {{ old('gender',$sleeper->gender)==='M'?'selected':'' }}>Homme</option>
                <option value="F" {{ old('gender',$sleeper->gender)==='F'?'selected':'' }}>Femme</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Site assigné</label>
              <select name="site_id" class="form-select">
                <option value="">Pool général</option>
                @foreach($sites as $s)
                <option value="{{ $s->id }}" {{ old('site_id',$sleeper->site_id)==$s->id?'selected':'' }}>{{ $s->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes', $sleeper->notes) }}</textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="active" value="1" id="activeCheck"
                       {{ $sleeper->active ? 'checked' : '' }}>
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
