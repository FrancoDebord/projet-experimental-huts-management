@extends('layouts.app')
@section('title', 'Incidents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="fw-bold mb-0">Gestion des incidents</h5>
  <a href="{{ route('incidents.create') }}" class="btn btn-primary">
    <i class="fa-solid fa-plus me-1"></i>Signaler un incident
  </a>
</div>

{{-- Filters --}}
<div class="card mb-4">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
          <option value="">Tous les statuts</option>
          <option value="open"        {{ request('status')==='open'        ?'selected':'' }}>Ouvert</option>
          <option value="in_progress" {{ request('status')==='in_progress' ?'selected':'' }}>En traitement</option>
          <option value="resolved"    {{ request('status')==='resolved'    ?'selected':'' }}>Résolu</option>
          <option value="closed"      {{ request('status')==='closed'      ?'selected':'' }}>Clôturé</option>
        </select>
      </div>
      <div class="col-md-3">
        <select name="severity" class="form-select form-select-sm">
          <option value="">Toutes sévérités</option>
          <option value="low"      {{ request('severity')==='low'      ?'selected':'' }}>Faible</option>
          <option value="medium"   {{ request('severity')==='medium'   ?'selected':'' }}>Moyen</option>
          <option value="high"     {{ request('severity')==='high'     ?'selected':'' }}>Élevé</option>
          <option value="critical" {{ request('severity')==='critical' ?'selected':'' }}>Critique</option>
        </select>
      </div>
      <div class="col-md-3">
        <select name="hut_id" class="form-select form-select-sm">
          <option value="">Toutes les cases</option>
          @foreach($huts as $h)
            <option value="{{ $h->id }}" {{ request('hut_id')==$h->id?'selected':'' }}>{{ $h->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
        <a href="{{ route('incidents.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr><th>Date</th><th>Case</th><th>Titre</th><th>Projet</th><th>Sévérité</th><th>Statut</th><th>Rapporté par</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($incidents as $inc)
          <tr>
            <td><small>{{ $inc->incident_date->format('d/m/Y') }}</small></td>
            <td>{{ $inc->hut?->name ?? '<em class="text-muted">—</em>' }}</td>
            <td class="fw-semibold">{{ Str::limit($inc->title, 40) }}</td>
            <td><small>{{ $inc->project?->project_code ?? '—' }}</small></td>
            <td><span class="badge bg-{{ $inc->severity_color }}">{{ $inc->severity_label }}</span></td>
            <td><span class="badge bg-{{ $inc->status_color }}">{{ $inc->status_label }}</span></td>
            <td><small class="text-muted">{{ $inc->reporter?->name ?? '—' }}</small></td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('incidents.show', $inc) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                <a href="{{ route('incidents.edit', $inc) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center py-4 text-muted">Aucun incident trouvé</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $incidents->withQueryString()->links() }}</div>
  </div>
</div>
@endsection
