@extends('layouts.app')
@section('title', 'Projet '.$project->project_code)

@section('content')
{{-- Project header --}}
<div class="row mb-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h4 class="fw-bold mb-1">{{ $project->project_code }}</h4>
            <p class="text-muted mb-2">{{ $project->project_title }}</p>
            <span class="badge bg-{{ $project->stage_color }} me-1">{{ $project->stage_label }}</span>
            @if($project->is_glp) <span class="badge bg-dark">GLP</span> @endif
          </div>
          <div class="d-flex gap-2">
            @php $blockedStagesHeader = ['not_started','suspended','completed','archived','NA']; @endphp
            @if(!in_array($project->project_stage, $blockedStagesHeader))
              <a href="{{ route('assignments.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-plus me-1"></i>Affecter cases
              </a>
            @endif
            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
              <i class="fa-solid fa-arrow-left me-1"></i>Retour
            </a>
          </div>
        </div>

        @if($project->description_project)
          <p class="text-muted small mt-2">{{ $project->description_project }}</p>
        @endif

        <div class="row g-2 mt-2 text-center">
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Durée totale</small>
            <strong>{{ $project->duration_days ?? '—' }}j</strong>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Jours écoulés</small>
            <strong>{{ $project->days_elapsed }}j</strong>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Jours restants</small>
            <strong>{{ $project->days_remaining }}j</strong>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Avancement</small>
            <strong>{{ $project->progress_percent }}%</strong>
          </div>
        </div>

        @if($project->duration_days)
        <div class="progress mt-3" style="height:10px">
          <div class="progress-bar" style="width:{{ $project->progress_percent }}%" role="progressbar"></div>
        </div>
        <div class="d-flex justify-content-between mt-1">
          <small class="text-muted">{{ $project->date_debut_effective?->format('d/m/Y') }}</small>
          <small class="text-muted">{{ $project->date_fin_effective?->format('d/m/Y') }}</small>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header">Cases utilisées — résumé</div>
      <div class="card-body">
        <div class="row g-2 text-center">
          <div class="col-6">
            <div class="p-2 rounded" style="background:rgba(204,0,0,0.1)">
              <div class="fw-bold fs-4" style="color:var(--airid-red)">{{ $stats['huts_count'] }}</div>
              <small class="text-muted">Cases distinctes</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded bg-light">
              <div class="fw-bold fs-4">{{ $stats['phases_count'] }}</div>
              <small class="text-muted">Phases</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded bg-light">
              <div class="fw-bold fs-4">{{ $stats['total_days'] }}</div>
              <small class="text-muted">Jours totaux</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded" style="background:#d1f7e0">
              <div class="fw-bold fs-4 text-success">{{ $stats['active_usages'] }}</div>
              <small class="text-muted">Utilisations actives</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Exp huts activities from pro_studies_activities --}}
@if($expHutsActivities->isNotEmpty())
<div class="card mb-4">
  <div class="card-header"><i class="fa-solid fa-flask me-2 text-primary"></i>Activités "Cases Expérimentales" du projet</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead><tr><th>Activité</th><th>Date prévue</th><th>Date fin prévue</th><th>Date effective</th><th>Statut</th></tr></thead>
        <tbody>
          @foreach($expHutsActivities as $act)
          <tr>
            <td class="fw-semibold">{{ $act->study_activity_name }}</td>
            <td>{{ $act->estimated_activity_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $act->estimated_activity_end_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $act->actual_activity_date?->format('d/m/Y') ?? '—' }}</td>
            <td>
              @php
                $sc = match($act->status) {
                  'completed' => 'success', 'pending' => 'warning',
                  'cancelled' => 'secondary', default => 'info'
                };
              @endphp
              <span class="badge bg-{{ $sc }}">{{ $act->status }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

{{-- Usages by phase --}}
@php
  $blockedStages = ['not_started', 'suspended', 'completed', 'archived', 'NA'];
  $isBlocked = in_array($project->project_stage, $blockedStages);
@endphp

@if($usagesByPhase->isEmpty())
  <div class="card text-center py-4">
    <div class="card-body text-muted">
      <i class="fa-solid fa-house fa-3x mb-2"></i><br>
      Aucune case expérimentale affectée à ce projet.
      <div class="mt-2">
        @if($isBlocked)
          <div class="alert alert-warning d-inline-block py-1 px-3 small mb-2">
            <i class="fa-solid fa-lock me-1"></i>
            Affectation bloquée — statut du projet : <strong>{{ $project->stage_label }}</strong>
          </div><br>
          <button class="btn btn-sm btn-secondary" disabled>
            <i class="fa-solid fa-plus me-1"></i>Affecter des cases
          </button>
        @else
          <a href="{{ route('assignments.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-plus me-1"></i>Affecter des cases
          </a>
        @endif
      </div>
    </div>
  </div>
@else
  @foreach($usagesByPhase as $phase => $phaseUsages)
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="fa-solid fa-layer-group me-2"></i>{{ $phase ?: 'Sans phase spécifiée' }}</span>
      <span class="badge bg-secondary">{{ $phaseUsages->count() }} case(s)</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead><tr><th>Case</th><th>Site</th><th>Début</th><th>Fin</th><th>Durée</th><th>Avancement</th><th>Statut</th></tr></thead>
          <tbody>
          @foreach($phaseUsages->sortBy('date_start') as $u)
          <tr>
            <td>
              <a href="{{ route('huts.show', $u->hut) }}" class="fw-semibold">{{ $u->hut->name }}</a>
            </td>
            <td><small class="text-muted">{{ $u->hut->site->name }}</small></td>
            <td>{{ $u->date_start->format('d/m/Y') }}</td>
            <td>{{ $u->date_end->format('d/m/Y') }}</td>
            <td>{{ $u->duration_in_days }}j</td>
            <td style="min-width:120px">
              <div class="progress mb-1"><div class="progress-bar" style="width:{{ $u->progress_percent }}%"></div></div>
              <small class="text-muted">{{ $u->days_elapsed }}j / {{ $u->duration_in_days }}j · {{ $u->progress_percent }}%</small>
            </td>
            <td><span class="badge bg-{{ $u->status_color }}">{{ $u->status_label }}</span></td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endforeach
@endif
@endsection
