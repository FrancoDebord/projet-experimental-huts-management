@extends('layouts.app')
@section('title', 'Incident – '.$incident->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">{{ $incident->title }}</h5>
    <div>
      <span class="badge bg-{{ $incident->severity_color }}">{{ $incident->severity_label }}</span>
      <span class="badge bg-{{ $incident->status_color }} ms-1">{{ $incident->status_label }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-primary btn-sm">
      <i class="fa-solid fa-pen me-1"></i>Modifier
    </a>
    <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="fa-solid fa-arrow-left me-1"></i>Retour
    </a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">Description</div>
      <div class="card-body">
        <p>{{ $incident->description }}</p>
        @if($incident->resolution_notes)
        <hr>
        <h6 class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Notes de résolution</h6>
        <p class="text-muted">{{ $incident->resolution_notes }}</p>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">Informations</div>
      <div class="card-body">
        <dl class="row mb-0" style="font-size:0.875rem">
          <dt class="col-6 text-muted">Date</dt>
          <dd class="col-6">{{ $incident->incident_date->format('d/m/Y') }}</dd>
          <dt class="col-6 text-muted">Case</dt>
          <dd class="col-6">
            @if($incident->hut)
              <a href="{{ route('huts.show', $incident->hut) }}">{{ $incident->hut->name }}</a>
            @else —
            @endif
          </dd>
          <dt class="col-6 text-muted">Projet</dt>
          <dd class="col-6">
            @if($incident->project)
              <a href="{{ route('projects.show', $incident->project) }}">{{ $incident->project->project_code }}</a>
            @else —
            @endif
          </dd>
          <dt class="col-6 text-muted">Rapporté par</dt>
          <dd class="col-6">{{ $incident->reporter?->name ?? '—' }}</dd>
          @if($incident->resolved_at)
          <dt class="col-6 text-muted">Résolu le</dt>
          <dd class="col-6">{{ $incident->resolved_at->format('d/m/Y') }}</dd>
          <dt class="col-6 text-muted">Résolu par</dt>
          <dd class="col-6">{{ $incident->resolver?->name ?? '—' }}</dd>
          @endif
        </dl>
      </div>
    </div>
  </div>
</div>
@endsection
