@extends('layouts.app')
@section('title', 'Dormeurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="fw-bold mb-0">Gestion des dormeurs</h5>
    <small class="text-muted">{{ $sleepers->whereNull('deleted_at')->count() }} actif(s)</small>
  </div>
  <a href="{{ route('sleepers.create') }}" class="btn btn-primary">
    <i class="fa-solid fa-plus me-1"></i>Nouveau dormeur
  </a>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="sleepersTable">
        <thead>
          <tr><th>Code</th><th>Nom</th><th>Genre</th><th>Site</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @foreach($sleepers as $sl)
          <tr class="{{ $sl->trashed() ? 'table-secondary opacity-50' : '' }}">
            <td><span class="badge bg-dark">{{ $sl->code }}</span></td>
            <td class="fw-semibold">{{ $sl->name }}</td>
            <td>
              @if($sl->gender === 'M') <span class="badge bg-primary">Homme</span>
              @elseif($sl->gender === 'F') <span class="badge bg-danger">Femme</span>
              @else <span class="text-muted">—</span>
              @endif
            </td>
            <td><small class="text-muted">{{ $sl->site?->name ?? 'Pool général' }}</small></td>
            <td>
              @if($sl->trashed())
                <span class="badge bg-danger">Supprimé</span>
              @elseif($sl->active)
                <span class="badge bg-success">Actif</span>
              @else
                <span class="badge bg-secondary">Inactif</span>
              @endif
            </td>
            <td>
              <div class="d-flex gap-1">
                @if(!$sl->trashed())
                  <a href="{{ route('sleepers.edit', $sl) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                  <form action="{{ route('sleepers.destroy', $sl) }}" method="POST" onsubmit="return confirm('Désactiver ce dormeur ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-trash"></i></button>
                  </form>
                @endif
                @if($isAdmin)
                  @if($sl->trashed())
                    <form action="{{ route('sleepers.restore', $sl->id) }}" method="POST">
                      @csrf
                      <button class="btn btn-sm btn-outline-success"><i class="fa-solid fa-rotate-left"></i></button>
                    </form>
                    <form action="{{ route('sleepers.force-delete', $sl->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ?')">
                      @csrf
                      <button class="btn btn-sm btn-danger"><i class="fa-solid fa-skull"></i></button>
                    </form>
                  @endif
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
  $('#sleepersTable').DataTable({
    pageLength: 25,
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json' },
    order: [[0,'asc']]
  });
});
</script>
@endpush
