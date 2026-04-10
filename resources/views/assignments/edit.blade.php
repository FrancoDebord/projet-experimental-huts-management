@extends('layouts.app')
@section('title', 'Modifier session — '.$assignment->project?->project_code)

@section('content')
<div class="row justify-content-center">
<div class="col-xl-8">

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0">
    <i class="fa-solid fa-pen me-2 text-primary"></i>
    Modifier la session — {{ $assignment->project?->project_code }}
    @if($assignment->phase_name)
      <span class="text-muted small">/ {{ $assignment->phase_name }}</span>
    @endif
  </h5>
  <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-outline-secondary">
    <i class="fa-solid fa-arrow-left me-1"></i>Retour
  </a>
</div>

<div class="card mb-4">
  <div class="card-header fw-bold">
    <i class="fa-solid fa-calendar-days me-2 text-primary"></i>Période & informations
  </div>
  <div class="card-body">
    <form method="POST" action="{{ url('/assignments/'.$assignment->id) }}">
      @csrf @method('PATCH')
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Phase / Désignation</label>
          <input type="text" name="phase_name" class="form-control"
                 value="{{ old('phase_name', $assignment->phase_name) }}"
                 placeholder="Ex: Phase 1, Round 2…">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Date de début <span class="text-danger">*</span></label>
          <input type="date" name="date_start" class="form-control @error('date_start') is-invalid @enderror"
                 value="{{ old('date_start', $assignment->date_start->format('Y-m-d')) }}" required>
          @error('date_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Date de fin <span class="text-danger">*</span></label>
          <input type="date" name="date_end" class="form-control @error('date_end') is-invalid @enderror"
                 value="{{ old('date_end', $assignment->date_end->format('Y-m-d')) }}" required>
          @error('date_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Notes</label>
          <textarea name="notes" class="form-control" rows="3"
                    placeholder="Informations complémentaires…">{{ old('notes', $assignment->notes) }}</textarea>
        </div>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4">
          <i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer les modifications
        </button>
        <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-outline-secondary">Annuler</a>
      </div>
    </form>
  </div>
</div>

{{-- Cases affectées (lecture seule) --}}
<div class="card">
  <div class="card-header fw-bold">
    <i class="fa-solid fa-house me-2 text-primary"></i>Cases affectées à cette session
    <span class="badge bg-secondary ms-2">{{ $assignment->projectUsages->count() }}</span>
  </div>
  <div class="card-body">
    <div class="row g-2">
      @foreach($assignment->projectUsages->sortBy(fn($u) => $u->hut?->number) as $usage)
      @php $hut = $usage->hut; @endphp
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="text-center border rounded p-2 {{ $hut?->status === 'in_use' ? 'border-primary' : '' }}">
          <i class="fa-solid fa-house mb-1 text-primary"></i>
          <div class="fw-bold small">{{ $hut?->number }}</div>
          <small class="text-muted" style="font-size:.7rem">{{ $hut?->site?->name }}</small>
        </div>
      </div>
      @endforeach
    </div>
    <small class="text-muted mt-2 d-block">
      <i class="fa-solid fa-circle-info me-1"></i>
      Pour modifier les cases affectées, annulez cette session et créez-en une nouvelle.
    </small>
  </div>
</div>

</div>
</div>
@endsection
