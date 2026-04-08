@extends('layouts.app')
@section('title', 'Affecter des cases')

@push('styles')
<style>
/* ── Stepper ── */
.stepper { display:flex; align-items:center; gap:0; margin-bottom:2rem; }
.step-item { flex:1; text-align:center; }
.step-circle {
  width:42px; height:42px; border-radius:50%;
  background:#e9ecef; color:#6c757d;
  display:inline-flex; align-items:center; justify-content:center;
  font-weight:700; font-size:1rem; margin-bottom:4px;
  border:3px solid #e9ecef; transition:all .3s;
}
.step-item.active .step-circle { background:var(--airid-red); color:#fff; border-color:var(--airid-red); }
.step-item.done .step-circle   { background:#198754; color:#fff; border-color:#198754; }
.step-line { flex:1; height:3px; background:#e9ecef; }
.step-item.done ~ .step-line, .step-item.active ~ .step-line { background:#198754; }
.step-label { font-size:.75rem; font-weight:600; color:#6c757d; }
.step-item.active .step-label  { color:var(--airid-red); }
.step-item.done .step-label    { color:#198754; }

/* ── Hut card selector ── */
.hut-selector { cursor:pointer; border:2px solid #e9ecef; border-radius:8px; padding:.6rem; text-align:center; transition:all .2s; user-select:none; }
.hut-selector:hover { border-color:var(--airid-red); transform:translateY(-1px); }
.hut-selector.selected { border-color:var(--airid-red); background:rgba(204,0,0,0.06); }
.hut-selector.selected .hut-num { color:var(--airid-red); }
.hut-selector.unavailable { opacity:.4; pointer-events:none; background:#f8f8f8; }
.hut-check { display:none; }
.hut-num { font-weight:700; font-size:1.1rem; }

/* ── Matrix ── */
.matrix-wrapper { overflow-x:auto; }
.matrix-table th, .matrix-table td { white-space:nowrap; font-size:.78rem; }
.matrix-table .hut-col { min-width:110px; }
.matrix-table .date-col { min-width:90px; font-weight:600; position:sticky; left:0; background:#1A1A1A; color:#fff; z-index:2; }
.matrix-table select { font-size:.75rem; padding:.15rem .3rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-xl-11">

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="fa-solid fa-house me-2 text-primary"></i>Affecter des cases à un projet</h5>
  @if($project)
    <span class="badge" style="background:var(--airid-red);font-size:.85rem">{{ $project->project_code }}</span>
  @endif
</div>

{{-- Stepper --}}
<div class="stepper">
  <div class="step-item active" id="step-indicator-1">
    <div class="step-circle">1</div><br>
    <span class="step-label">Sélection des cases</span>
  </div>
  <div class="step-line" id="line-1-2"></div>
  <div class="step-item" id="step-indicator-2">
    <div class="step-circle">2</div><br>
    <span class="step-label">Période & phase</span>
  </div>
  <div class="step-line" id="line-2-3"></div>
  <div class="step-item" id="step-indicator-3">
    <div class="step-circle">3</div><br>
    <span class="step-label">Planning dormeurs</span>
  </div>
</div>

<form method="POST" action="{{ route('assignments.store') }}" id="wizardForm">
@csrf
@if($project)
  <input type="hidden" name="project_id" value="{{ $project->id }}">
@endif

{{-- ═══════════════════════════════════════════════════════════════
     STEP 1 — Select huts
═══════════════════════════════════════════════════════════════ --}}
<div id="step1" class="step-panel">
  <div class="card">
    <div class="card-header fw-bold">
      <i class="fa-solid fa-house me-2 text-primary"></i>Étape 1 — Sélectionnez les cases à affecter
    </div>
    <div class="card-body">
      {{-- Project selector if not pre-set --}}
      @if(!$project)
      <div class="mb-4">
        <label class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
        <select name="project_id" id="projectSelect" class="form-select select2" required>
          <option value="">— Choisir un projet en cours —</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}">{{ $p->project_code }} — {{ Str::limit($p->project_title, 50) }}</option>
          @endforeach
        </select>
        <small class="text-muted">Seuls les projets « En cours » sont disponibles.</small>
      </div>
      @endif

      {{-- Site tabs --}}
      <ul class="nav nav-tabs mb-3" id="siteTabs">
        @foreach($sites as $i => $site)
        <li class="nav-item">
          <a class="nav-link {{ $i===0?'active':'' }}" data-bs-toggle="tab" href="#site-{{ $site->id }}">
            {{ $site->name }}
            <span class="badge bg-secondary ms-1" id="count-{{ $site->id }}">0</span>
          </a>
        </li>
        @endforeach
      </ul>

      <div class="tab-content">
        @foreach($sites as $i => $site)
        <div class="tab-pane fade {{ $i===0?'show active':'' }}" id="site-{{ $site->id }}">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">{{ $site->name }} — {{ $site->huts->count() }} case(s)</small>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-xs btn-outline-primary select-all-site" data-site="{{ $site->id }}">
                <i class="fa-solid fa-check-double me-1"></i>Tout sélectionner
              </button>
              <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-site" data-site="{{ $site->id }}">
                Désélectionner tout
              </button>
            </div>
          </div>
          <div class="row g-2">
            @foreach($site->huts->sortBy('number') as $hut)
            @php
              $available = $hut->status !== 'damaged' && $hut->status !== 'abandoned';
              $cur = $hut->currentUsage();
            @endphp
            <div class="col-4 col-sm-3 col-md-2 col-lg-2">
              <div class="hut-selector {{ !$available ? 'unavailable' : '' }} {{ $hut->status }}"
                   data-hut-id="{{ $hut->id }}"
                   data-site="{{ $site->id }}"
                   title="{{ !$available ? $hut->status_label : 'Cliquer pour sélectionner' }}">
                <input type="checkbox" name="hut_ids[]" value="{{ $hut->id }}" class="hut-check" id="hut-{{ $hut->id }}">
                <div class="hut-num">{{ $hut->number }}</div>
                {!! $hut->status_badge !!}
                @if($cur)
                  <div style="font-size:.62rem;color:var(--airid-red)">{{ $cur->project?->project_code }}</div>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>

      {{-- Selected summary --}}
      <div class="alert alert-light border mt-3" id="selectionSummary">
        <i class="fa-solid fa-circle-info me-2 text-primary"></i>
        Aucune case sélectionnée. Cliquez sur les cases pour les sélectionner.
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-4" id="nextStep1"
              onclick="goToStep(2)" disabled>
        Suivant <i class="fa-solid fa-arrow-right ms-1"></i>
      </button>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     STEP 2 — Period & phase
═══════════════════════════════════════════════════════════════ --}}
<div id="step2" class="step-panel d-none">
  <div class="card">
    <div class="card-header fw-bold">
      <i class="fa-solid fa-calendar-days me-2 text-primary"></i>Étape 2 — Période d'utilisation
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Phase / Désignation</label>
          <input type="text" name="phase_name" class="form-control" placeholder="Ex: Phase 1, Round 2…">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Date de début <span class="text-danger">*</span></label>
          <input type="date" name="date_start" id="dateStart" class="form-control" required
                 onchange="updateMatrix()">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Date de fin <span class="text-danger">*</span></label>
          <input type="date" name="date_end" id="dateEnd" class="form-control" required
                 onchange="updateMatrix()">
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Notes</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="Informations complémentaires…"></textarea>
        </div>
        <div class="col-12">
          <div class="alert alert-info py-2" id="durationInfo" style="display:none">
            <i class="fa-solid fa-calendar me-2"></i>
            Durée : <strong id="durationDisplay">—</strong>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
      <button type="button" class="btn btn-outline-secondary" onclick="goToStep(1)">
        <i class="fa-solid fa-arrow-left me-1"></i>Retour
      </button>
      <button type="button" class="btn btn-primary px-4" onclick="goToStep(3)">
        Suivant <i class="fa-solid fa-arrow-right ms-1"></i>
      </button>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     STEP 3 — Sleeper rotation matrix
═══════════════════════════════════════════════════════════════ --}}
<div id="step3" class="step-panel d-none">
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between fw-bold">
      <span><i class="fa-solid fa-person me-2 text-primary"></i>Étape 3 — Planning des dormeurs (optionnel)</span>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-success" onclick="autoRotate()">
          <i class="fa-solid fa-rotate me-1"></i>Auto-rotation
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearMatrix()">
          <i class="fa-solid fa-eraser me-1"></i>Effacer
        </button>
      </div>
    </div>
    <div class="card-body">
      <div class="alert alert-light border py-2 mb-3">
        <small>
          <i class="fa-solid fa-circle-info me-1 text-primary"></i>
          Le tableau ci-dessous permet d'assigner un dormeur par case pour chaque date de l'activité.
          Utilisez <strong>Auto-rotation</strong> pour une attribution automatique équilibrée.
          Vous pouvez également sauter cette étape et renseigner les dormeurs plus tard.
        </small>
      </div>

      {{-- Matrix generated by JS --}}
      <div id="matrixContainer" class="matrix-wrapper">
        <div class="text-center py-5 text-muted" id="matrixPlaceholder">
          <i class="fa-solid fa-table fa-3x mb-2"></i><br>
          Le tableau apparaîtra une fois la période renseignée à l'étape 2.
        </div>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
      <button type="button" class="btn btn-outline-secondary" onclick="goToStep(2)">
        <i class="fa-solid fa-arrow-left me-1"></i>Retour
      </button>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" onclick="submitForm(false)">
          Enregistrer sans dormeurs
        </button>
        <button type="submit" class="btn btn-primary px-4">
          <i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer avec dormeurs
        </button>
      </div>
    </div>
  </div>
</div>

</form>
</div>
</div>
@endsection

@push('scripts')
<script>
const sleepersData = {!! json_encode($sleepers->map(fn($s) => ['id' => $s->id, 'code' => $s->code, 'name' => $s->name])) !!};

let selectedHuts = []; // [{id, name, number}]
let selectedDates = [];

// ── Stepper navigation ──────────────────────────────────────────────────────
function goToStep(n) {
  if (n === 2) {
    if (!validateStep1()) return;
  }
  if (n === 3) {
    if (!validateStep2()) return;
    generateMatrix();
  }
  [1,2,3].forEach(i => {
    document.getElementById('step' + i).classList.add('d-none');
    const ind = document.getElementById('step-indicator-' + i);
    ind.classList.remove('active','done');
    if (i < n) ind.classList.add('done');
    if (i === n) ind.classList.add('active');
  });
  document.getElementById('step' + n).classList.remove('d-none');
  window.scrollTo({top: 0, behavior: 'smooth'});
}

// ── Step 1: Hut selection ───────────────────────────────────────────────────
function toggleHut(el) {
  el.classList.toggle('selected');
  const cb = el.querySelector('.hut-check');
  cb.checked = el.classList.contains('selected');
  updateSelection();
}

function updateSelection() {
  selectedHuts = [];
  document.querySelectorAll('.hut-selector.selected').forEach(el => {
    const cb = el.querySelector('.hut-check');
    selectedHuts.push({ id: cb.value, name: el.closest('[data-hut-id]')?.dataset?.hutId, num: el.querySelector('.hut-num').textContent.trim() });
  });

  // Update site badges
  document.querySelectorAll('.site-filter-tab, [id^="count-"]').forEach(badge => {
    const siteId = badge.id.replace('count-','');
    const cnt = document.querySelectorAll(`.hut-selector.selected[data-site="${siteId}"]`).length;
    badge.textContent = cnt;
  });

  const total = selectedHuts.length;
  const summary = document.getElementById('selectionSummary');
  if (total === 0) {
    summary.innerHTML = '<i class="fa-solid fa-circle-info me-2 text-primary"></i>Aucune case sélectionnée.';
  } else {
    summary.innerHTML = `<i class="fa-solid fa-check-circle me-2 text-success"></i><strong>${total} case(s) sélectionnée(s)</strong>`;
  }
  document.getElementById('nextStep1').disabled = total === 0;
}

// Select / deselect all per site
document.querySelectorAll('.select-all-site').forEach(btn => {
  btn.addEventListener('click', function() {
    const siteId = this.dataset.site;
    document.querySelectorAll(`.hut-selector[data-site="${siteId}"]:not(.unavailable)`).forEach(el => {
      el.classList.add('selected');
      el.querySelector('.hut-check').checked = true;
    });
    updateSelection();
  });
});
document.querySelectorAll('.deselect-all-site').forEach(btn => {
  btn.addEventListener('click', function() {
    const siteId = this.dataset.site;
    document.querySelectorAll(`.hut-selector[data-site="${siteId}"]`).forEach(el => {
      el.classList.remove('selected');
      el.querySelector('.hut-check').checked = false;
    });
    updateSelection();
  });
});

function validateStep1() {
  @if(!$project)
  const projSel = document.getElementById('projectSelect');
  if (!projSel.value) { alert('Veuillez choisir un projet.'); return false; }
  @endif
  if (selectedHuts.length === 0) { alert('Veuillez sélectionner au moins une case.'); return false; }
  return true;
}

// ── Step 2: Period ──────────────────────────────────────────────────────────
function validateStep2() {
  const s = document.getElementById('dateStart').value;
  const e = document.getElementById('dateEnd').value;
  if (!s || !e) { alert('Veuillez renseigner les dates de début et de fin.'); return false; }
  if (s > e) { alert('La date de fin doit être après la date de début.'); return false; }
  return true;
}

function updateMatrix() {
  const s = document.getElementById('dateStart').value;
  const e = document.getElementById('dateEnd').value;
  if (s && e && s <= e) {
    const start = new Date(s), end = new Date(e);
    const days = Math.floor((end - start) / 86400000) + 1;
    document.getElementById('durationInfo').style.display = '';
    document.getElementById('durationDisplay').textContent = `${days} jour(s) — du ${formatDate(s)} au ${formatDate(e)}`;

    // Build dates array
    selectedDates = [];
    for (let d = new Date(s); d <= end; d.setDate(d.getDate()+1)) {
      selectedDates.push(d.toISOString().split('T')[0]);
    }
  }
}

function formatDate(d) {
  const [y,m,day] = d.split('-');
  return `${day}/${m}/${y}`;
}

// ── Step 3: Matrix generation ───────────────────────────────────────────────
function generateMatrix() {
  const container = document.getElementById('matrixContainer');
  if (!selectedHuts.length || !selectedDates.length) {
    container.innerHTML = '<div class="alert alert-warning">Sélectionnez des cases et une période d\'abord.</div>';
    return;
  }

  // Get selected hut info from DOM
  const huts = [];
  document.querySelectorAll('.hut-selector.selected').forEach(el => {
    huts.push({
      id: el.querySelector('.hut-check').value,
      label: el.querySelector('.hut-num').textContent.trim()
    });
  });

  let html = `<table class="table table-bordered table-sm matrix-table">
  <thead>
    <tr>
      <th class="date-col" style="background:#1A1A1A;color:#fff">Date</th>`;
  huts.forEach(h => {
    html += `<th class="hut-col text-center">Case ${h.label}</th>`;
  });
  html += `</tr></thead><tbody>`;

  selectedDates.forEach((date, di) => {
    html += `<tr>
      <td class="date-col" style="background:#1A1A1A;color:#fff">${formatDate(date)}</td>`;
    huts.forEach(h => {
      html += `<td class="hut-col">
        <select name="sleepers[${date}][${h.id}]" class="form-select form-select-sm sleeper-select"
                data-date="${date}" data-hut="${h.id}" data-di="${di}" data-hi="${huts.indexOf(h)}">
          <option value="">—</option>`;
      sleepersData.forEach(s => {
        html += `<option value="${s.id}">${s.code} – ${s.name}</option>`;
      });
      html += `</select></td>`;
    });
    html += '</tr>';
  });
  html += '</tbody></table>';

  container.innerHTML = html;
  document.getElementById('matrixPlaceholder')?.remove();
}

// ── Auto-rotation algorithm ─────────────────────────────────────────────────
function autoRotate() {
  if (!sleepersData.length) { alert('Aucun dormeur enregistré. Ajoutez des dormeurs dans Gestion > Dormeurs.'); return; }
  const selects = document.querySelectorAll('.sleeper-select');
  if (!selects.length) { alert('Générez le tableau d\'abord (revenez à l\'étape 2 et re-saisissez les dates).'); return; }

  selects.forEach(sel => {
    const di = parseInt(sel.dataset.di);
    const hi = parseInt(sel.dataset.hi);
    const idx = (di + hi) % sleepersData.length;
    sel.value = sleepersData[idx].id;
  });
}

function clearMatrix() {
  document.querySelectorAll('.sleeper-select').forEach(sel => sel.value = '');
}

function submitForm(withSleepers) {
  if (!withSleepers) {
    document.querySelectorAll('.sleeper-select').forEach(sel => sel.disabled = true);
  }
  document.getElementById('wizardForm').submit();
}

// Init Select2
$(document).ready(function() {
  $('#projectSelect').select2({ theme: 'bootstrap-5', placeholder: '— Choisir un projet —' });
});
</script>
@endpush
