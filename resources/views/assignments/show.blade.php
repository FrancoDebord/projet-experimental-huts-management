@extends('layouts.app')
@section('title', 'Session — '.$assignment->project?->project_code)

@push('styles')
<style>
.matrix-wrapper { overflow-x:auto; }
.matrix-table th,.matrix-table td { white-space:nowrap; font-size:.78rem; }
.date-sticky { position:sticky; left:0; background:#1A1A1A!important; color:#fff!important; z-index:2; min-width:90px; }
</style>
@endpush

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      Session — {{ $assignment->project?->project_code }}
      @if($assignment->phase_name)<span class="text-muted">/ {{ $assignment->phase_name }}</span>@endif
    </h4>
    <div>
      <span class="badge bg-{{ $assignment->status_color }}">{{ $assignment->status_label }}</span>
      <span class="text-muted ms-2 small">
        {{ $assignment->date_start->format('d/m/Y') }} → {{ $assignment->date_end->format('d/m/Y') }}
        ({{ $assignment->duration_in_days }}j)
      </span>
    </div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    {{-- Utiliser $assignment->status (champ DB) et non current_status (calculé) --}}
    @if(!in_array($assignment->status, ['completed','cancelled']))
    {{-- Modifier --}}
    <a href="{{ url('/assignments/'.$assignment->id.'/edit') }}" class="btn btn-outline-primary btn-sm">
      <i class="fa-solid fa-pen me-1"></i>Modifier
    </a>
    @endif
    {{-- Annuler (soft delete) : restreint au super_admin si la date est passée --}}
    @if(!$assignment->trashed() && (!$assignment->date_end->isPast() || auth()->user()->role === 'super_admin'))
    <form action="{{ route('assignments.destroy', $assignment) }}" method="POST">
      @csrf @method('DELETE')
      <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Annuler cette session ? Elle sera restaurable.')">
        <i class="fa-solid fa-xmark me-1"></i>Annuler
      </button>
    </form>
    @endif
    {{-- Restaurer / Supprimer définitivement (super_admin uniquement) --}}
    @if(auth()->user()->role === 'super_admin')
      @if($assignment->trashed())
      <form action="{{ url('/assignments/'.$assignment->id.'/restore') }}" method="POST">
        @csrf
        <button class="btn btn-warning btn-sm" onclick="return confirm('Restaurer cette session ?')">
          <i class="fa-solid fa-rotate-left me-1"></i>Restaurer
        </button>
      </form>
      @endif
      <form action="{{ url('/assignments/'.$assignment->id.'/force-delete') }}" method="POST">
        @csrf
        <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer DÉFINITIVEMENT cette session ? Action irréversible.')">
          <i class="fa-solid fa-trash me-1"></i>Supprimer
        </button>
      </form>
    @endif
  </div>
</div>

{{-- Bouton libération cases : visible dès que la date est passée et session non annulée --}}
@if($assignment->date_end->isPast() && $assignment->status !== 'cancelled')
@php $hasInUse = $huts->contains(fn($h) => $h->status === 'in_use'); @endphp
<div class="alert alert-persistent {{ $hasInUse || $assignment->status !== 'completed' ? 'alert-warning' : 'alert-success' }} d-flex align-items-center gap-3 mb-3">
  <i class="fa-solid fa-{{ $hasInUse || $assignment->status !== 'completed' ? 'triangle-exclamation' : 'circle-check' }} fa-2x flex-shrink-0"></i>
  <div class="flex-grow-1">
    @if($hasInUse || $assignment->status !== 'completed')
      <strong>Date de fin dépassée — cases non encore libérées.</strong><br>
      <small>Cliquez sur le bouton pour terminer la session et remettre les {{ $huts->count() }} case(s) à « Disponible ».</small>
    @else
      <strong>Session terminée.</strong>
      <small class="ms-1">Les {{ $huts->count() }} case(s) ont été remises à « Disponible ».</small>
    @endif
  </div>
  @if($hasInUse || $assignment->status !== 'completed')
  <form action="{{ route('assignments.complete', $assignment) }}" method="POST" class="flex-shrink-0">
    @csrf @method('PATCH')
    <button class="btn btn-warning fw-semibold"
            onclick="return confirm('Terminer la session et libérer les {{ $huts->count() }} case(s) ?')">
      <i class="fa-solid fa-flag-checkered me-1"></i>Terminer &amp; libérer les cases
    </button>
  </form>
  @endif
</div>
@endif

{{-- Progress bar --}}
<div class="card mb-3">
  <div class="card-body py-2">
    <div class="d-flex justify-content-between mb-1 small">
      <span class="text-muted">{{ $assignment->date_start->format('d/m/Y') }}</span>
      <span class="fw-semibold">{{ $assignment->progress_percent }}% — {{ $assignment->days_elapsed }}j / {{ $assignment->duration_in_days }}j</span>
      <span class="text-muted">{{ $assignment->date_end->format('d/m/Y') }}</span>
    </div>
    <div class="progress" style="height:12px;border-radius:20px">
      <div class="progress-bar" style="width:{{ $assignment->progress_percent }}%;background:var(--airid-red)" role="progressbar">
        @if($assignment->progress_percent > 10){{ $assignment->progress_percent }}%@endif
      </div>
    </div>
    <div class="d-flex gap-4 mt-1 small text-muted justify-content-center">
      <span><i class="fa-solid fa-clock text-primary me-1"></i>Écoulé : <strong>{{ $assignment->days_elapsed }}j</strong></span>
      <span><i class="fa-solid fa-hourglass-half text-warning me-1"></i>Restant : <strong>{{ $assignment->days_remaining }}j</strong></span>
      <span><i class="fa-solid fa-house text-success me-1"></i>Cases : <strong>{{ $huts->count() }}</strong></span>
    </div>
  </div>
</div>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-huts">Cases</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-matrix">Dormeurs</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-obs">Observations ({{ $assignment->dailyObservations->count() }})</a></li>
</ul>

<div class="tab-content">
  {{-- Cases --}}
  <div class="tab-pane fade show active" id="tab-huts">
    <div class="row g-2">
      @foreach($huts as $hut)
      <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('huts.show', $hut) }}" class="text-decoration-none">
          <div class="hut-card {{ $hut->status }} p-2 text-center">
            <div class="hut-icon"><i class="fa-solid fa-house"></i></div>
            <div class="fw-bold small">{{ $hut->number }}</div>
            <small class="text-muted">{{ $hut->site->name }}</small>
          </div>
        </a>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Dormeurs matrix --}}
  <div class="tab-pane fade" id="tab-matrix">
    @php
      $dates = $assignment->dates;
      $assignments = $assignment->sleeperAssignments->groupBy(fn($a) => $a->assignment_date->format('Y-m-d'));
    @endphp

    <form method="POST" action="{{ route('assignments.sleepers', $assignment) }}" class="mb-3">
      @csrf
      <div class="d-flex justify-content-end gap-2 mb-2">
        <button type="button" class="btn btn-sm btn-outline-success" onclick="autoFill()">
          <i class="fa-solid fa-rotate me-1"></i>Auto-rotation
        </button>
        <button type="submit" class="btn btn-sm btn-primary">
          <i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer le planning
        </button>
      </div>

      <div class="matrix-wrapper">
        <table class="table table-bordered table-sm matrix-table">
          <thead>
            <tr>
              <th class="date-sticky">Date</th>
              @foreach($huts as $hut)
              <th class="text-center" style="min-width:110px">Case {{ $hut->number }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($dates as $date)
            <tr>
              <td class="date-sticky fw-semibold">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</td>
              @foreach($huts as $hut)
              @php
                $assigned = $assignments->get($date)?->firstWhere('hut_id', $hut->id);
              @endphp
              <td>
                <select name="sleepers[{{ $date }}][{{ $hut->id }}]"
                        class="form-select form-select-sm sleeper-sel"
                        data-di="{{ array_search($date, $dates) }}"
                        data-hi="{{ $huts->values()->search(fn($h) => $h->id === $hut->id) }}">
                  <option value="">—</option>
                  @foreach($sleepers as $sl)
                  <option value="{{ $sl->id }}" {{ $assigned?->sleeper_id === $sl->id ? 'selected' : '' }}>
                    {{ $sl->code }} – {{ $sl->name }}
                  </option>
                  @endforeach
                </select>
              </td>
              @endforeach
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </form>
  </div>

  {{-- Observations --}}
  <div class="tab-pane fade" id="tab-obs">
    <div class="row">
      <div class="col-lg-7">
        @forelse($assignment->dailyObservations as $obs)
        <div class="card mb-2">
          <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <span class="fw-semibold small">{{ $obs->observation_date->format('d/m/Y') }}
                  @if($obs->hut) — Case {{ $obs->hut->number }} @endif
                </span>
                <small class="text-muted ms-2">{{ $obs->observer?->name ?? '—' }}</small>
              </div>
              {{-- Supprimer observation : auteur ou admin --}}
              @if($obs->observed_by === auth()->id() || in_array(auth()->user()->role, ['super_admin','facility_manager']))
              <form action="{{ url('/assignments/'.$assignment->id.'/observations/'.$obs->id) }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn btn-link btn-sm text-danger p-0 ms-2" title="Supprimer"
                        onclick="return confirm('Supprimer cette observation ?')">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </form>
              @endif
            </div>
            <p class="mb-0 mt-1 small">{{ $obs->observation }}</p>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-3">Aucune observation enregistrée</div>
        @endforelse
      </div>
      <div class="col-lg-5">
        <div class="card">
          <div class="card-header fw-semibold small">Ajouter une observation</div>
          <div class="card-body">
            <form method="POST" action="{{ route('assignments.observations', $assignment) }}">
              @csrf
              <div class="mb-2">
                <label class="form-label small fw-semibold">Date</label>
                <input type="date" name="observation_date" class="form-control form-control-sm"
                       value="{{ now()->format('Y-m-d') }}" required>
              </div>
              <div class="mb-2">
                <label class="form-label small fw-semibold">Case (optionnel)</label>
                <select name="hut_id" class="form-select form-select-sm">
                  <option value="">Général (pas de case spécifique)</option>
                  @foreach($huts as $hut)
                  <option value="{{ $hut->id }}">Case {{ $hut->number }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label small fw-semibold">Observation <span class="text-danger">*</span></label>
                <textarea name="observation" rows="3" class="form-control form-control-sm" required
                          placeholder="Observations du superviseur…"></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const sleepersData = {!! json_encode($sleepers->map(fn($s) => ['id' => $s->id, 'code' => $s->code])) !!};

function autoFill() {
  if (!sleepersData.length) { alert('Aucun dormeur disponible.'); return; }
  document.querySelectorAll('.sleeper-sel').forEach(sel => {
    const di = parseInt(sel.dataset.di);
    const hi = parseInt(sel.dataset.hi);
    const idx = (di + hi) % sleepersData.length;
    sel.value = sleepersData[idx].id;
  });
}

function confirmComplete(btn) {
  const count = {{ $huts->count() }};
  if (confirm(`Marquer cette session comme terminée ?\n\n${count} case(s) seront remises à « Disponible » et pourront être réaffectées.`)) {
    btn.closest('form').submit();
  }
}
</script>
@endpush
