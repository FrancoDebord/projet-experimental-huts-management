@extends('layouts.app')
@section('title', 'Signaler un incident')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header"><i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Signaler un incident</div>
      <div class="card-body">
        <form method="POST" action="{{ route('incidents.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Case concernée</label>
              <select name="hut_id" class="form-select select2">
                <option value="">— Non associé à une case —</option>
                @foreach($huts as $h)
                  <option value="{{ $h->id }}" {{ old('hut_id', request('hut_id'))==$h->id?'selected':'' }}>
                    {{ $h->name }} ({{ $h->site->name }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Projet associé</label>
              <select name="project_id" class="form-select select2">
                <option value="">— Aucun projet —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}" {{ old('project_id')==$p->id?'selected':'' }}>{{ $p->project_code }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                     value="{{ old('title') }}" required placeholder="Résumé court de l'incident">
              @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
              <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                        required placeholder="Description détaillée de l'incident…">{{ old('description') }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Date de l'incident <span class="text-danger">*</span></label>
              <input type="date" name="incident_date" class="form-control"
                     value="{{ old('incident_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Sévérité</label>
              <select name="severity" class="form-select">
                <option value="low"      {{ old('severity')==='low'     ?'selected':'' }}>Faible</option>
                <option value="medium"   {{ old('severity')==='medium'  ?'selected':'' }}>Moyen</option>
                <option value="high"     {{ old('severity')==='high'    ?'selected':'' }}>Élevé</option>
                <option value="critical" {{ old('severity')==='critical'?'selected':'' }}>Critique</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Statut</label>
              <select name="status" class="form-select">
                <option value="open"        {{ old('status')==='open'        ?'selected':'' }}>Ouvert</option>
                <option value="in_progress" {{ old('status')==='in_progress' ?'selected':'' }}>En traitement</option>
                <option value="resolved"    {{ old('status')==='resolved'    ?'selected':'' }}>Résolu</option>
                <option value="closed"      {{ old('status')==='closed'      ?'selected':'' }}>Clôturé</option>
              </select>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-danger px-4"><i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer</button>
            <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>$('.select2').select2({ theme: 'bootstrap-5' });</script>
@endpush
