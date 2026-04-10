@extends('layouts.app')
@section('title', 'Projet '.$project->project_code)

@push('styles')
<style>
/* ── Accordion session ── */
.session-header {
  cursor: pointer;
  transition: background .2s;
}
.session-header:hover { background: rgba(204,0,0,0.04); }
.session-header .arrow { transition: transform .3s; }
.session-header[aria-expanded="true"] .arrow { transform: rotate(180deg); }

/* ── Hut mini-card ── */
.hut-mini {
  border: 2px solid #e9ecef;
  border-radius: 10px;
  padding: .55rem .4rem;
  text-align: center;
  cursor: pointer;
  transition: all .2s;
  position: relative;
}
.hut-mini:hover { border-color: var(--airid-red); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.hut-mini.has-incident { border-color: #dc3545 !important; }
.hut-incident-dot {
  position: absolute; top: 4px; right: 4px;
  width: 8px; height: 8px; border-radius: 50%; background: #dc3545;
}
.hut-num { font-weight: 700; font-size: 1rem; color: #1a1a1a; }
.hut-mini .site-lbl { font-size: .65rem; color: #888; }

/* ── Matrix table ── */
.matrix-ro-wrapper { overflow-x: auto; }
.matrix-ro th, .matrix-ro td { font-size: .75rem; white-space: nowrap; padding: .3rem .4rem !important; }
.matrix-ro .date-hd { position: sticky; left: 0; background: #1a1a1a; color: #fff; z-index: 2; min-width: 85px; }
.cell-ok       { background: #f0fdf4; }
.cell-incident { background: #fff1f1; }
.cell-empty    { background: #fafafa; color: #bbb; }
.incident-pill { font-size: .62rem; display: inline-block; }

/* ── Progress bar ── */
.progress-thin { height: 8px; border-radius: 20px; }
.progress-styled .progress-bar { background: linear-gradient(90deg, var(--airid-red), #ff6b6b); border-radius: 20px; }

/* ── Modal hut detail ── */
#hutModal .modal-header { background: #1a1a1a; color: #fff; }
#hutModal .modal-header .btn-close { filter: invert(1); }
.dt-row-incident { background: #fff5f5 !important; }
.dt-row-ok       { }
.severity-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 3px; }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════════
     EN-TÊTE PROJET
══════════════════════════════════════════════════════════════════════════ --}}
<div class="row mb-4 g-3">

  {{-- Info projet --}}
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <h4 class="fw-bold mb-1">{{ $project->project_code }}</h4>
            <p class="text-muted mb-2">{{ $project->project_title }}</p>
            <span class="badge bg-{{ $project->stage_color }} me-1">{{ $project->stage_label }}</span>
            @if($project->is_glp) <span class="badge bg-dark">GLP</span> @endif
          </div>
          <div class="d-flex gap-2 flex-wrap">
            @php $blockedStages = ['not_started','suspended','completed','archived','NA']; @endphp
            @if(!in_array($project->project_stage, $blockedStages))
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
          <p class="text-muted small mt-2 mb-2">{{ $project->description_project }}</p>
        @endif

        <div class="row g-2 mt-2 text-center">
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Sessions</small>
            <strong>{{ $stats['sessions_count'] }}</strong>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Cases distinctes</small>
            <strong>{{ $stats['huts_count'] }}</strong>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Jours totaux</small>
            <strong>{{ $stats['total_days'] }}j</strong>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Incidents</small>
            <strong class="{{ $incidents->count() ? 'text-danger' : '' }}">{{ $incidents->count() }}</strong>
          </div>
        </div>

        @if($project->duration_days)
        <div class="mt-3">
          <div class="d-flex justify-content-between mb-1 small">
            <span class="text-muted">{{ $project->date_debut_effective?->format('d/m/Y') }}</span>
            <span class="fw-semibold">{{ $project->progress_percent }}% — {{ $project->days_elapsed }}j / {{ $project->duration_days }}j</span>
            <span class="text-muted">{{ $project->date_fin_effective?->format('d/m/Y') }}</span>
          </div>
          <div class="progress progress-styled progress-thin">
            <div class="progress-bar" style="width:{{ $project->progress_percent }}%" role="progressbar"></div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Stats cases + incidents --}}
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header fw-semibold small">Résumé cases &amp; incidents</div>
      <div class="card-body">
        <div class="row g-2 text-center">
          <div class="col-6">
            <div class="p-2 rounded" style="background:rgba(204,0,0,.1)">
              <div class="fw-bold fs-4" style="color:var(--airid-red)">{{ $stats['huts_count'] }}</div>
              <small class="text-muted">Cases utilisées</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded" style="background:{{ $stats['active_count'] ? '#d1f7e0' : '#f8f9fa' }}">
              <div class="fw-bold fs-4 {{ $stats['active_count'] ? 'text-success' : 'text-muted' }}">{{ $stats['active_count'] }}</div>
              <small class="text-muted">Session(s) active(s)</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded bg-light">
              <div class="fw-bold fs-4">{{ $stats['sessions_count'] }}</div>
              <small class="text-muted">Session(s) / phase(s)</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded {{ $incidents->where('status','open')->count() ? 'bg-danger bg-opacity-10' : 'bg-light' }}">
              <div class="fw-bold fs-4 {{ $incidents->where('status','open')->count() ? 'text-danger' : 'text-muted' }}">
                {{ $incidents->where('status','open')->count() }}
              </div>
              <small class="text-muted">Incident(s) ouvert(s)</small>
            </div>
          </div>
        </div>
        @if(in_array($project->project_stage, $blockedStages))
        <div class="alert alert-warning py-2 px-3 small mt-3 mb-0">
          <i class="fa-solid fa-lock me-1"></i>Affectation bloquée — statut : <strong>{{ $project->stage_label }}</strong>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     ACTIVITÉS "EXPERIMENTAL HUTS" du projet
══════════════════════════════════════════════════════════════════════════ --}}
@if($expHutsActivities->isNotEmpty())
<div class="card mb-4">
  <div class="card-header fw-semibold small">
    <i class="fa-solid fa-flask me-2 text-primary"></i>Activités "Cases Expérimentales" planifiées
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th>Activité</th>
            <th>Date prévue</th>
            <th>Date fin prévue</th>
            <th>Date effective</th>
            <th>Statut</th>
          </tr>
        </thead>
        <tbody>
          @foreach($expHutsActivities as $act)
          <tr>
            <td class="fw-semibold">{{ $act->study_activity_name }}</td>
            <td>{{ $act->estimated_activity_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $act->estimated_activity_end_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $act->actual_activity_date?->format('d/m/Y') ?? '—' }}</td>
            <td>
              @php $sc = match($act->status) { 'completed'=>'success','pending'=>'warning','cancelled'=>'secondary',default=>'info' }; @endphp
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

{{-- ══════════════════════════════════════════════════════════════════════════
     HISTORIQUE DES SESSIONS / PHASES
══════════════════════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0">
    <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>
    Historique des affectations
    <span class="badge bg-secondary ms-1">{{ $sessions->count() }}</span>
  </h5>
  @if(!in_array($project->project_stage, $blockedStages))
  <a href="{{ route('assignments.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
    <i class="fa-solid fa-plus me-1"></i>Nouvelle session
  </a>
  @endif
</div>

@if($sessions->isEmpty())
<div class="card text-center py-5">
  <div class="card-body text-muted">
    <i class="fa-solid fa-house fa-3x mb-3 opacity-25"></i>
    <p class="mb-2">Aucune case expérimentale affectée à ce projet.</p>
    @if(!in_array($project->project_stage, $blockedStages))
      <a href="{{ route('assignments.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
        <i class="fa-solid fa-plus me-1"></i>Affecter des cases
      </a>
    @endif
  </div>
</div>
@else

{{-- Accordion --}}
<div class="accordion" id="sessionsAccordion">
@foreach($sessions as $si => $session)
@php
  $sessionHuts      = $session->projectUsages->sortBy(fn($u) => $u->hut?->number);
  $sessionIncidents = $incidents->filter(fn($i) =>
    $session->projectUsages->pluck('hut_id')->contains($i->hut_id) &&
    $i->incident_date->between($session->date_start, $session->date_end)
  );
  $hutIncidentIds   = $sessionIncidents->pluck('hut_id')->unique();
  $isOpen           = $si === 0; // première session ouverte par défaut
@endphp

<div class="card mb-3 border-0 shadow-sm {{ $session->trashed() ? 'opacity-75' : '' }}">

  {{-- ── Accordion Header ── --}}
  <div class="card-header session-header p-0"
       data-bs-toggle="collapse"
       data-bs-target="#sess-{{ $session->id }}"
       aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
       aria-controls="sess-{{ $session->id }}">
    <div class="d-flex align-items-center justify-content-between px-3 py-2 flex-wrap gap-2">

      {{-- Titre + badges --}}
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <i class="fa-solid fa-chevron-down arrow text-muted small"></i>
        <span class="fw-bold">
          {{ $session->phase_name ?: 'Phase sans nom' }}
        </span>
        <span class="badge bg-{{ $session->status_color }}">{{ $session->status_label }}</span>
        @if($session->trashed())
          <span class="badge bg-secondary">Annulée</span>
        @endif
        @if($sessionIncidents->isNotEmpty())
          <span class="badge bg-danger">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $sessionIncidents->count() }} incident(s)
          </span>
        @endif
      </div>

      {{-- Méta --}}
      <div class="d-flex gap-3 align-items-center text-muted small flex-wrap">
        <span><i class="fa-solid fa-calendar me-1"></i>{{ $session->date_start->format('d/m/Y') }} → {{ $session->date_end->format('d/m/Y') }}</span>
        <span><i class="fa-solid fa-house me-1"></i>{{ $sessionHuts->count() }} case(s)</span>
        <span><i class="fa-solid fa-clock me-1"></i>{{ $session->duration_in_days }}j</span>
        @if($session->creator)
          <span class="d-none d-md-inline"><i class="fa-solid fa-user me-1"></i>{{ $session->creator->name }}</span>
        @endif
      </div>
    </div>

    {{-- Barre de progression --}}
    <div class="px-3 pb-2">
      <div class="progress progress-styled progress-thin">
        <div class="progress-bar" style="width:{{ $session->progress_percent }}%" role="progressbar"
             title="{{ $session->days_elapsed }}j / {{ $session->duration_in_days }}j — {{ $session->progress_percent }}%">
        </div>
      </div>
      <div class="d-flex justify-content-between mt-1" style="font-size:.68rem;color:#aaa">
        <span>{{ $session->date_start->format('d/m/Y') }}</span>
        <span class="fw-semibold" style="color:#666">{{ $session->progress_percent }}% — {{ $session->days_elapsed }}j / {{ $session->duration_in_days }}j</span>
        <span>{{ $session->date_end->format('d/m/Y') }}</span>
      </div>
    </div>
  </div>

  {{-- ── Accordion Body ── --}}
  <div id="sess-{{ $session->id }}" class="collapse {{ $isOpen ? 'show' : '' }}" data-bs-parent="#sessionsAccordion">
    <div class="card-body p-0">

      {{-- Actions --}}
      <div class="d-flex gap-2 flex-wrap px-3 py-2 border-bottom bg-light">
        <a href="{{ route('assignments.show', $session) }}" class="btn btn-xs btn-outline-primary btn-sm">
          <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Voir la session complète
        </a>
        @if(!$session->trashed() && !in_array($session->current_status, ['completed','cancelled']))
        <a href="{{ url('/assignments/'.$session->id.'/edit') }}" class="btn btn-xs btn-outline-secondary btn-sm">
          <i class="fa-solid fa-pen me-1"></i>Modifier
        </a>
        <form action="{{ route('assignments.complete', $session) }}" method="POST" class="d-inline">
          @csrf @method('PATCH')
          <button class="btn btn-xs btn-outline-success btn-sm" onclick="return confirm('Terminer cette session ?')">
            <i class="fa-solid fa-flag-checkered me-1"></i>Terminer
          </button>
        </form>
        @endif
        @if(!$session->trashed())
        <form action="{{ route('assignments.destroy', $session) }}" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button class="btn btn-xs btn-outline-danger btn-sm" onclick="return confirm('Annuler cette session ?')">
            <i class="fa-solid fa-xmark me-1"></i>Annuler
          </button>
        </form>
        @endif
        @if(in_array(auth()->user()->role, ['super_admin','facility_manager']))
          @if($session->trashed())
          <form action="{{ url('/assignments/'.$session->id.'/restore') }}" method="POST" class="d-inline">
            @csrf <button class="btn btn-xs btn-warning btn-sm">
              <i class="fa-solid fa-rotate-left me-1"></i>Restaurer
            </button>
          </form>
          @endif
          <form action="{{ url('/assignments/'.$session->id.'/force-delete') }}" method="POST" class="d-inline">
            @csrf <button class="btn btn-xs btn-danger btn-sm"
                          onclick="return confirm('Supprimer définitivement ?')">
              <i class="fa-solid fa-trash me-1"></i>Supprimer
            </button>
          </form>
        @endif
      </div>

      {{-- Tabs --}}
      <ul class="nav nav-tabs px-3 pt-2" id="tab-nav-{{ $session->id }}">
        <li class="nav-item">
          <a class="nav-link active small" data-bs-toggle="tab" href="#tab-huts-{{ $session->id }}">
            <i class="fa-solid fa-house me-1"></i>Cases ({{ $sessionHuts->count() }})
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link small" data-bs-toggle="tab" href="#tab-matrix-{{ $session->id }}">
            <i class="fa-solid fa-table me-1"></i>Rotation dormeurs
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link small" data-bs-toggle="tab" href="#tab-obs-{{ $session->id }}">
            <i class="fa-solid fa-clipboard me-1"></i>Observations ({{ $session->dailyObservations->count() }})
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link small {{ $sessionIncidents->isNotEmpty() ? 'text-danger fw-bold' : '' }}"
             data-bs-toggle="tab" href="#tab-inc-{{ $session->id }}">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>Incidents ({{ $sessionIncidents->count() }})
          </a>
        </li>
      </ul>

      <div class="tab-content p-3">

        {{-- ── Tab Cases ── --}}
        <div class="tab-pane fade show active" id="tab-huts-{{ $session->id }}">
          @if($session->notes)
            <div class="alert alert-light border small py-2 mb-3">
              <i class="fa-solid fa-note-sticky me-1 text-muted"></i>{{ $session->notes }}
            </div>
          @endif
          <div class="row g-2">
            @foreach($sessionHuts as $usage)
            @php $hut = $usage->hut; $hasInc = $hut && $hutIncidentIds->contains($hut->id); @endphp
            @if($hut)
            <div class="col-4 col-sm-3 col-md-2 col-lg-2">
              <div class="hut-mini {{ $hasInc ? 'has-incident' : '' }}"
                   onclick="openHutModal({{ $session->id }}, {{ $hut->id }})"
                   title="Cliquer pour voir le détail">
                @if($hasInc)<div class="hut-incident-dot" title="Incident signalé"></div>@endif
                <i class="fa-solid fa-house mb-1 text-primary d-block"></i>
                <div class="hut-num">{{ $hut->number }}</div>
                <div class="site-lbl">{{ $hut->site?->name }}</div>
                <span class="badge bg-{{ $hut->status_color }} mt-1" style="font-size:.6rem">{{ $hut->status_label }}</span>
              </div>
            </div>
            @endif
            @endforeach
          </div>
          <small class="text-muted mt-2 d-block">
            <i class="fa-solid fa-circle-info me-1"></i>Cliquer sur une case pour voir le détail par date (dormeur, observations, incidents).
          </small>
        </div>

        {{-- ── Tab Matrice dormeurs ── --}}
        <div class="tab-pane fade" id="tab-matrix-{{ $session->id }}">
          @php
            $matrixHuts = $sessionHuts->filter(fn($u) => $u->hut)->map(fn($u) => $u->hut)->sortBy('number');
            $matrixDates = $session->dates;
            $slpIdx = $session->sleeperAssignments->groupBy(
              fn($a) => \Carbon\Carbon::parse($a->assignment_date)->format('Y-m-d')
            );
          @endphp

          @if($matrixHuts->isEmpty() || empty($matrixDates))
            <div class="text-muted text-center py-3">Aucune donnée à afficher.</div>
          @else
          <div class="matrix-ro-wrapper">
            <table class="table table-bordered table-sm matrix-ro mb-0">
              <thead class="table-dark">
                <tr>
                  <th class="date-hd">Date</th>
                  @foreach($matrixHuts as $mHut)
                    <th class="text-center" style="min-width:95px">Case {{ $mHut->number }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach($matrixDates as $mDate)
                @php
                  $dayAssignments = $slpIdx[$mDate] ?? collect();
                  $dayIncidents   = $sessionIncidents->filter(fn($i) => $i->incident_date->format('Y-m-d') === $mDate);
                @endphp
                <tr>
                  <td class="date-hd fw-semibold">
                    {{ \Carbon\Carbon::parse($mDate)->translatedFormat('D d/m') }}
                    @if($dayIncidents->isNotEmpty())
                      <span class="ms-1" title="{{ $dayIncidents->count() }} incident(s)">⚠️</span>
                    @endif
                  </td>
                  @foreach($matrixHuts as $mHut)
                  @php
                    $asgn     = $dayAssignments->firstWhere('hut_id', $mHut->id);
                    $cellInc  = $sessionIncidents->filter(fn($i) => $i->hut_id == $mHut->id && $i->incident_date->format('Y-m-d') === $mDate);
                  @endphp
                  <td class="text-center {{ $cellInc->isNotEmpty() ? 'cell-incident' : ($asgn ? 'cell-ok' : 'cell-empty') }}"
                      title="{{ $asgn ? ($asgn->sleeper?->code.' — '.$asgn->sleeper?->name) : '—' }}">
                    @if($asgn && $asgn->sleeper)
                      <span class="fw-semibold" style="font-size:.75rem">{{ $asgn->sleeper->code }}</span><br>
                      <span style="font-size:.65rem;color:#666">{{ Str::limit($asgn->sleeper->name, 10) }}</span>
                    @else
                      <span style="color:#ccc">—</span>
                    @endif
                    @if($cellInc->isNotEmpty())
                      @foreach($cellInc as $ci)
                        <span class="incident-pill badge bg-{{ $ci->severity_color }}" title="{{ $ci->title }}">
                          ⚠ {{ $ci->severity_label }}
                        </span>
                      @endforeach
                    @endif
                  </td>
                  @endforeach
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="d-flex gap-3 mt-2 small text-muted">
            <span><span class="badge bg-success opacity-50">&nbsp;&nbsp;&nbsp;</span> Dormeur assigné</span>
            <span><span class="badge bg-danger opacity-25">&nbsp;&nbsp;&nbsp;</span> Incident ce jour</span>
            <span><span class="badge bg-light border">&nbsp;&nbsp;&nbsp;</span> Non assigné</span>
          </div>
          @endif
        </div>

        {{-- ── Tab Observations ── --}}
        <div class="tab-pane fade" id="tab-obs-{{ $session->id }}">
          @forelse($session->dailyObservations as $obs)
          <div class="d-flex gap-3 border-bottom py-2 align-items-start">
            <div class="text-center flex-shrink-0" style="min-width:70px">
              <span class="badge bg-secondary" style="font-size:.7rem">{{ $obs->observation_date->format('d/m/Y') }}</span>
              @if($obs->hut)
                <div style="font-size:.65rem;color:#888;margin-top:2px">Case {{ $obs->hut->number }}</div>
              @else
                <div style="font-size:.65rem;color:#888;margin-top:2px">Général</div>
              @endif
            </div>
            <div class="flex-grow-1">
              <p class="mb-0 small">{{ $obs->observation }}</p>
              <small class="text-muted">— {{ $obs->observer?->name ?? 'Inconnu' }}</small>
            </div>
          </div>
          @empty
          <div class="text-center text-muted py-3 small">
            <i class="fa-solid fa-clipboard fa-2x mb-2 opacity-25 d-block"></i>
            Aucune observation pour cette session.
          </div>
          @endforelse
        </div>

        {{-- ── Tab Incidents ── --}}
        <div class="tab-pane fade" id="tab-inc-{{ $session->id }}">
          @forelse($sessionIncidents->sortByDesc('incident_date') as $inc)
          <div class="d-flex gap-3 border-bottom py-2 align-items-start">
            <div class="flex-shrink-0 text-center" style="min-width:80px">
              <span class="badge bg-{{ $inc->severity_color }}" style="font-size:.7rem">{{ $inc->severity_label }}</span>
              <div style="font-size:.65rem;color:#888;margin-top:2px">{{ $inc->incident_date->format('d/m/Y') }}</div>
              @if($inc->hut)
                <div style="font-size:.65rem;color:var(--airid-red)">Case {{ $inc->hut->number }}</div>
              @endif
            </div>
            <div class="flex-grow-1">
              <div class="fw-semibold small">
                {{ $inc->title }}
                <span class="badge bg-{{ $inc->status_color }} ms-1" style="font-size:.6rem">{{ $inc->status_label }}</span>
              </div>
              <p class="text-muted small mb-1">{{ Str::limit($inc->description, 120) }}</p>
              <small class="text-muted">Signalé par {{ $inc->reporter?->name ?? '—' }}</small>
            </div>
            <a href="{{ route('incidents.show', $inc) }}" class="btn btn-xs btn-outline-secondary btn-sm flex-shrink-0">
              <i class="fa-solid fa-eye"></i>
            </a>
          </div>
          @empty
          <div class="text-center text-muted py-3 small">
            <i class="fa-solid fa-shield-halved fa-2x mb-2 opacity-25 d-block"></i>
            Aucun incident durant cette session.
          </div>
          @endforelse
        </div>

      </div>{{-- end tab-content --}}
    </div>{{-- end card-body --}}
  </div>{{-- end collapse --}}
</div>{{-- end card --}}

@endforeach
</div>{{-- end accordion --}}
@endif

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL DÉTAIL CASE
══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="hutModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0" id="hutModalTitle">Détail de la case</h5>
          <small id="hutModalSub" class="text-white-50"></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">

        {{-- Info case --}}
        <div class="px-4 py-3 border-bottom bg-light d-flex gap-4 flex-wrap align-items-center">
          <div><small class="text-muted d-block">Site</small><strong id="hutModalSite">—</strong></div>
          <div><small class="text-muted d-block">Statut</small><span id="hutModalStatus">—</span></div>
          <div><small class="text-muted d-block">Dormeurs distincts</small><strong id="hutModalSleeperCount">—</strong></div>
          <div><small class="text-muted d-block">Jours avec incident</small><strong id="hutModalIncCount" class="text-danger">—</strong></div>
          <div id="hutModalNotesWrap" class="d-none"><small class="text-muted d-block">Notes</small><span id="hutModalNotes" class="small text-muted">—</span></div>
        </div>

        {{-- Légende --}}
        <div class="px-4 py-2 border-bottom d-flex gap-3 flex-wrap" style="font-size:.75rem">
          <span style="color:#166534"><i class="fa-solid fa-circle me-1"></i>Dormeur assigné</span>
          <span style="color:#dc2626"><i class="fa-solid fa-triangle-exclamation me-1"></i>Incident</span>
          <span style="color:#aaa"><i class="fa-solid fa-circle me-1"></i>Vide</span>
        </div>

        {{-- Table par date --}}
        <div class="table-responsive">
          <table class="table table-hover table-sm mb-0" id="hutModalTable">
            <thead class="table-dark">
              <tr>
                <th>Date</th>
                <th>Dormeur</th>
                <th>Observations</th>
                <th>Incidents</th>
              </tr>
            </thead>
            <tbody id="hutModalBody">
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <a id="hutModalLink" href="#" class="btn btn-sm btn-outline-primary" target="_blank">
          <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Voir la fiche de la case
        </a>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// ── Données pré-calculées par PHP ──────────────────────────────────────────
const sessionsJson = @json($sessionsJson);

function openHutModal(sessionId, hutId) {
  const session = sessionsJson[sessionId];
  if (!session) return;

  const hut = session.huts.find(h => h.id === hutId);
  if (!hut) return;

  // En-tête modal
  document.getElementById('hutModalTitle').textContent = `Case ${hut.number}`;
  document.getElementById('hutModalSub').textContent   = `${hut.site} — ${hut.status_label}`;
  document.getElementById('hutModalSite').textContent  = hut.site;
  document.getElementById('hutModalStatus').innerHTML  = `<span class="badge bg-secondary">${hut.status_label}</span>`;

  // Lien fiche case (approximatif via numéro — le back n'a pas de route directe par hut.id ici)
  document.getElementById('hutModalLink').href = `/huts/${hutId}`;

  // Notes
  if (hut.notes) {
    document.getElementById('hutModalNotes').textContent = hut.notes;
    document.getElementById('hutModalNotesWrap').classList.remove('d-none');
  } else {
    document.getElementById('hutModalNotesWrap').classList.add('d-none');
  }

  // Stats
  const uniqueSleepers = new Set(hut.dates.filter(d => d.sleeper).map(d => d.sleeper.code));
  const incDays        = hut.dates.filter(d => d.has_incident).length;
  document.getElementById('hutModalSleeperCount').textContent = uniqueSleepers.size || '—';
  document.getElementById('hutModalIncCount').textContent     = incDays || '0';

  // Lignes du tableau
  const tbody = document.getElementById('hutModalBody');
  tbody.innerHTML = '';

  hut.dates.forEach(day => {
    const tr = document.createElement('tr');
    if (day.has_incident) tr.classList.add('dt-row-incident');

    // Date
    const tdDate = document.createElement('td');
    tdDate.innerHTML = `<span class="fw-semibold" style="font-size:.8rem">${day.date_label}</span>`;
    tr.appendChild(tdDate);

    // Dormeur
    const tdSleeper = document.createElement('td');
    if (day.sleeper) {
      tdSleeper.innerHTML = `
        <span class="fw-semibold text-success" style="font-size:.8rem">${day.sleeper.code}</span>
        <span class="text-muted small ms-1">${day.sleeper.name}</span>`;
    } else {
      tdSleeper.innerHTML = `<span class="text-muted" style="font-size:.75rem">—</span>`;
    }
    tr.appendChild(tdSleeper);

    // Observations
    const tdObs = document.createElement('td');
    if (day.observations.length) {
      tdObs.innerHTML = day.observations.map(o =>
        `<div style="font-size:.75rem" class="text-muted border-start border-2 ps-2 mb-1">${escHtml(o)}</div>`
      ).join('');
    } else {
      tdObs.innerHTML = `<span class="text-muted" style="font-size:.75rem">—</span>`;
    }
    tr.appendChild(tdObs);

    // Incidents
    const tdInc = document.createElement('td');
    if (day.incidents.length) {
      tdInc.innerHTML = day.incidents.map(i =>
        `<a href="/incidents/${i.id}" class="d-block text-decoration-none mb-1" target="_blank">
          <span class="badge bg-${i.severity_color}" style="font-size:.65rem">⚠ ${i.severity_label}</span>
          <span class="small ms-1 text-dark">${escHtml(i.title)}</span>
          <span class="badge bg-${i.status_color} ms-1" style="font-size:.6rem">${i.status_label}</span>
        </a>`
      ).join('');
    } else {
      tdInc.innerHTML = `<span class="text-muted" style="font-size:.75rem">—</span>`;
    }
    tr.appendChild(tdInc);

    tbody.appendChild(tr);
  });

  const modal = new bootstrap.Modal(document.getElementById('hutModal'));
  modal.show();
}

function escHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
</script>
@endpush
