@extends('layouts.app')
@section('title', $hut->name)

@push('styles')
<style>
.nav-tabs .nav-link { color: var(--airid-gray); font-weight: 500; }
.nav-tabs .nav-link.active { color: var(--airid-red); border-bottom-color: var(--airid-red); font-weight: 600; }
</style>
@endpush

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <i class="fa-solid fa-house me-2 text-primary"></i>{{ $hut->name }}
      {!! $hut->status_badge !!}
    </h4>
    <a href="{{ route('sites.show', $hut->site) }}" class="text-muted small">
      <i class="fa-solid fa-location-dot me-1"></i>{{ $hut->site->name }}
    </a>
    @if($hut->hasCoordinates())
      <small class="text-muted ms-2"><i class="fa-solid fa-crosshairs me-1"></i>{{ $hut->latitude }}, {{ $hut->longitude }}</small>
    @endif
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('huts.edit', $hut) }}" class="btn btn-outline-primary btn-sm">
      <i class="fa-solid fa-pen me-1"></i>Modifier
    </a>
    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
      <i class="fa-solid fa-arrows-rotate me-1"></i>Changer état
    </button>
  </div>
</div>

@if($hut->image_path)
<div class="mb-4">
  <img src="{{ asset('storage/'.$hut->image_path) }}" alt="{{ $hut->name }}"
       style="max-height:220px;border-radius:12px;object-fit:cover;max-width:100%">
</div>
@endif

@if($hut->notes)
<div class="alert alert-light border mb-3"><i class="fa-solid fa-circle-info me-2 text-primary"></i>{{ $hut->notes }}</div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3" id="hutTabs">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-usages">
    <i class="fa-solid fa-calendar-days me-1"></i>Utilisations ({{ $hut->projectUsages->count() }})
  </a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-history">
    <i class="fa-solid fa-clock-rotate-left me-1"></i>Historique états ({{ $hut->stateChanges->count() }})
  </a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-incidents">
    <i class="fa-solid fa-triangle-exclamation me-1"></i>Incidents ({{ $hut->incidents->count() }})
  </a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-add-usage">
    <i class="fa-solid fa-plus me-1"></i>Affecter à un projet
  </a></li>
</ul>

<div class="tab-content">
  {{-- TAB: USAGES --}}
  <div class="tab-pane fade show active" id="tab-usages">
    @if($hut->projectUsages->isEmpty())
      <div class="text-center py-4 text-muted"><i class="fa-solid fa-calendar-xmark fa-2x mb-2"></i><br>Aucune utilisation enregistrée</div>
    @else
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Projet</th><th>Phase</th><th>Début</th><th>Fin</th><th>Durée</th><th>Avancement</th><th>Statut</th><th></th></tr></thead>
          <tbody>
          @foreach($hut->projectUsages->sortByDesc('date_start') as $u)
          <tr>
            <td>
              <a href="{{ route('projects.show', $u->project_id) }}" class="fw-semibold">
                {{ $u->project?->project_code ?? "Projet #$u->project_id" }}
              </a>
            </td>
            <td><small class="text-muted">{{ $u->phase_name ?: '—' }}</small></td>
            <td>{{ $u->date_start->format('d/m/Y') }}</td>
            <td>{{ $u->date_end->format('d/m/Y') }}</td>
            <td>{{ $u->duration_in_days }}j</td>
            <td style="min-width:100px">
              <div class="progress mb-1"><div class="progress-bar" style="width:{{ $u->progress_percent }}%"></div></div>
              <small class="text-muted">{{ $u->days_elapsed }}j / {{ $u->duration_in_days }}j ({{ $u->progress_percent }}%)</small>
            </td>
            <td><span class="badge bg-{{ $u->status_color }}">{{ $u->status_label }}</span></td>
            <td>
              <form action="{{ route('huts.remove-usage', [$hut, $u]) }}" method="POST"
                    onsubmit="return confirm('Supprimer cette utilisation ?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- TAB: STATE HISTORY --}}
  <div class="tab-pane fade" id="tab-history">
    @if($hut->stateChanges->isEmpty())
      <div class="text-center py-4 text-muted"><i class="fa-solid fa-clock-rotate-left fa-2x mb-2"></i><br>Aucun changement d'état enregistré</div>
    @else
      <ul class="timeline mt-2">
        @foreach($hut->stateChanges as $sc)
        <li class="timeline-item">
          <div class="timeline-icon"><i class="fa-solid fa-arrows-rotate"></i></div>
          <div class="timeline-body">
            <div class="d-flex justify-content-between">
              <span>
                <span class="badge bg-secondary me-1">{{ $sc->previous_status_label }}</span>
                <i class="fa-solid fa-arrow-right mx-1 text-muted"></i>
                <span class="badge bg-primary">{{ $sc->new_status_label }}</span>
              </span>
              <small class="text-muted">{{ $sc->changed_at->format('d/m/Y H:i') }}</small>
            </div>
            @if($sc->reason)
              <p class="mb-0 mt-1 small text-muted"><i class="fa-solid fa-comment me-1"></i>{{ $sc->reason }}</p>
            @endif
            @if($sc->changedBy)
              <small class="text-muted">Par {{ $sc->changedBy->name }}</small>
            @endif
          </div>
        </li>
        @endforeach
      </ul>
    @endif
  </div>

  {{-- TAB: INCIDENTS --}}
  <div class="tab-pane fade" id="tab-incidents">
    <div class="d-flex justify-content-end mb-2">
      <a href="{{ route('incidents.create') }}?hut_id={{ $hut->id }}" class="btn btn-sm btn-outline-danger">
        <i class="fa-solid fa-plus me-1"></i>Signaler incident
      </a>
    </div>
    @if($hut->incidents->isEmpty())
      <div class="text-center py-4 text-muted"><i class="fa-solid fa-shield-halved fa-2x mb-2"></i><br>Aucun incident enregistré</div>
    @else
      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Date</th><th>Titre</th><th>Sévérité</th><th>Statut</th><th></th></tr></thead>
          <tbody>
          @foreach($hut->incidents as $inc)
          <tr>
            <td>{{ $inc->incident_date->format('d/m/Y') }}</td>
            <td><a href="{{ route('incidents.show', $inc) }}">{{ $inc->title }}</a></td>
            <td><span class="badge bg-{{ $inc->severity_color }}">{{ $inc->severity_label }}</span></td>
            <td><span class="badge bg-{{ $inc->status_color }}">{{ $inc->status_label }}</span></td>
            <td><a href="{{ route('incidents.show', $inc) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- TAB: ADD USAGE --}}
  <div class="tab-pane fade" id="tab-add-usage">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header">Affecter cette case à un projet</div>
          <div class="card-body">
            <form method="POST" action="{{ route('huts.add-usage', $hut) }}">
              @csrf
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
                  <select name="project_id" class="form-select select2" required>
                    <option value="">— Choisir un projet —</option>
                    @foreach($projects as $p)
                      <option value="{{ $p->id }}">{{ $p->project_code }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Activité liée (optionnel)</label>
                  <select name="study_activity_id" class="form-select select2">
                    <option value="">— Aucune —</option>
                    @foreach($expHutsActivities as $act)
                      <option value="{{ $act->id }}"
                              data-start="{{ $act->estimated_activity_date?->format('Y-m-d') }}"
                              data-end="{{ $act->estimated_activity_end_date?->format('Y-m-d') }}">
                        Projet {{ $act->project?->project_code }} — {{ $act->estimated_activity_date?->format('d/m/Y') }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Phase / Description</label>
                  <input type="text" name="phase_name" class="form-control" placeholder="Ex: Phase 1, Round 2…">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Date début <span class="text-danger">*</span></label>
                  <input type="date" name="date_start" id="usageDateStart" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Date fin <span class="text-danger">*</span></label>
                  <input type="date" name="date_end" id="usageDateEnd" class="form-control" required>
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Notes</label>
                  <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
              </div>
              <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Status Change Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Changer l'état de la case</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('huts.update-status', $hut) }}">
        @csrf @method('PATCH')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nouvel état</label>
            <select name="status" class="form-select" required>
              <option value="available" {{ $hut->status==='available'?'selected':'' }}>Disponible</option>
              <option value="in_use"    {{ $hut->status==='in_use'?'selected':'' }}>En utilisation</option>
              <option value="damaged"   {{ $hut->status==='damaged'?'selected':'' }}>Endommagée</option>
              <option value="abandoned" {{ $hut->status==='abandoned'?'selected':'' }}>Abandonnée</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Raison du changement</label>
            <input type="text" name="reason" class="form-control" placeholder="Motif du changement d'état…">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Notes supplémentaires</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-warning">Confirmer le changement</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$('.select2').select2({ theme: 'bootstrap-5' });

// Auto-fill dates from linked activity
$('[name="study_activity_id"]').on('change', function() {
  const opt = this.options[this.selectedIndex];
  if (opt.dataset.start) document.getElementById('usageDateStart').value = opt.dataset.start;
  if (opt.dataset.end)   document.getElementById('usageDateEnd').value   = opt.dataset.end;
});
</script>
@endpush
