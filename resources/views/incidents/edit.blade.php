@extends('layouts.app')
@section('title', 'Modifier incident')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-pen me-2 text-primary"></i>Modifier l'incident</span>
        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-arrow-left me-1"></i>Retour
        </a>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('incidents.update', $incident) }}">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Case</label>
              <select name="hut_id" class="form-select select2">
                <option value="">— Aucune —</option>
                @foreach($huts as $h)
                  <option value="{{ $h->id }}" {{ old('hut_id', $incident->hut_id)==$h->id?'selected':'' }}>
                    {{ $h->name }} ({{ $h->site->name }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Projet</label>
              <select name="project_id" class="form-select select2">
                <option value="">— Aucun —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}" {{ old('project_id', $incident->project_id)==$p->id?'selected':'' }}>{{ $p->project_code }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" value="{{ old('title', $incident->title) }}" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
              <textarea name="description" rows="4" class="form-control" required>{{ old('description', $incident->description) }}</textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Date</label>
              <input type="date" name="incident_date" class="form-control" value="{{ old('incident_date', $incident->incident_date->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Sévérité</label>
              <select name="severity" class="form-select">
                @foreach(['low'=>'Faible','medium'=>'Moyen','high'=>'Élevé','critical'=>'Critique'] as $v => $l)
                  <option value="{{ $v }}" {{ old('severity', $incident->severity)===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Statut</label>
              <select name="status" class="form-select">
                @foreach(['open'=>'Ouvert','in_progress'=>'En traitement','resolved'=>'Résolu','closed'=>'Clôturé'] as $v => $l)
                  <option value="{{ $v }}" {{ old('status', $incident->status)===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes de résolution</label>
              <textarea name="resolution_notes" rows="3" class="form-control"
                        placeholder="Comment l'incident a été résolu…">{{ old('resolution_notes', $incident->resolution_notes) }}</textarea>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer</button>
            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">Annuler</a>
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
