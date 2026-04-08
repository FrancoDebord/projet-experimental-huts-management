@extends('layouts.app')
@section('title', 'Projets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="fw-bold mb-0">Projets utilisant les cases expérimentales</h5>
</div>

{{-- Filters --}}
<div class="card mb-4">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Rechercher par code ou titre…" value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select name="stage" class="form-select form-select-sm">
          <option value="">Tous les stades</option>
          <option value="not_started" {{ request('stage')==='not_started'?'selected':'' }}>Non démarré</option>
          <option value="in progress" {{ request('stage')==='in progress'?'selected':'' }}>En cours</option>
          <option value="suspended"   {{ request('stage')==='suspended'?'selected':'' }}>Suspendu</option>
          <option value="completed"   {{ request('stage')==='completed'?'selected':'' }}>Terminé</option>
          <option value="archived"    {{ request('stage')==='archived'?'selected':'' }}>Archivé</option>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="projectsTable">
        <thead>
          <tr>
            <th>Code</th>
            <th>Titre</th>
            <th>Stade</th>
            <th>Début effectif</th>
            <th>Fin effectif</th>
            <th>Avancement</th>
            <th>Cases</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($projects as $p)
          <tr>
            <td>
              <span class="fw-semibold">{{ $p->project_code }}</span>
              @if(in_array($p->id, $projectsWithUsages))
                <span class="badge ms-1" style="background:var(--airid-red);font-size:0.65rem">Cases</span>
              @endif
            </td>
            <td>{{ Str::limit($p->project_title, 40) }}</td>
            <td><span class="badge bg-{{ $p->stage_color }}">{{ $p->stage_label }}</span></td>
            <td><small>{{ $p->date_debut_effective?->format('d/m/Y') ?? '—' }}</small></td>
            <td><small>{{ $p->date_fin_effective?->format('d/m/Y') ?? '—' }}</small></td>
            <td style="min-width:100px">
              @if($p->duration_days)
                <div class="progress mb-1"><div class="progress-bar" style="width:{{ $p->progress_percent }}%"></div></div>
                <small class="text-muted">{{ $p->days_elapsed }}j / {{ $p->duration_days }}j</small>
              @else
                <small class="text-muted">—</small>
              @endif
            </td>
            <td>
              @php $cnt = $p->projectUsages()->count(); @endphp
              @if($cnt)
                <span class="badge bg-secondary">{{ $cnt }}</span>
              @else
                <span class="text-muted small">0</span>
              @endif
            </td>
            <td>
              <a href="{{ route('projects.show', $p) }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-eye"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $projects->withQueryString()->links() }}</div>
  </div>
</div>
@endsection
