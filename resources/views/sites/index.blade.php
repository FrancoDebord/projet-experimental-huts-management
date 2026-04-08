@extends('layouts.app')
@section('title', 'Sites')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="mb-0 fw-bold">Sites de cases expérimentales</h5>
    <small class="text-muted">{{ $sites->count() }} site(s) enregistré(s)</small>
  </div>
  <a href="{{ route('sites.create') }}" class="btn btn-primary">
    <i class="fa-solid fa-plus me-1"></i> Nouveau site
  </a>
</div>

<div class="row g-3">
  @forelse($sites as $site)
  <div class="col-md-6 col-xl-4">
    <div class="card h-100">
      @if($site->image_path)
        <img src="{{ asset('storage/' . $site->image_path) }}" class="card-img-top" style="height:160px;object-fit:cover;border-radius:10px 10px 0 0">
      @else
        <div class="d-flex align-items-center justify-content-center bg-light" style="height:100px;border-radius:10px 10px 0 0">
          <i class="fa-solid fa-location-dot fa-3x text-muted"></i>
        </div>
      @endif
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <h6 class="fw-bold mb-1">{{ $site->name }}</h6>
          {!! $site->status_badge !!}
        </div>
        @if($site->village || $site->city)
          <p class="text-muted small mb-2">
            <i class="fa-solid fa-map-pin me-1"></i>
            {{ implode(', ', array_filter([$site->village, $site->city])) }}
          </p>
        @endif
        @if($site->hasCoordinates())
          <p class="text-muted small mb-2">
            <i class="fa-solid fa-crosshairs me-1"></i>
            {{ number_format($site->latitude, 4) }}, {{ number_format($site->longitude, 4) }}
          </p>
        @endif

        {{-- Huts status mini-bar --}}
        @php
          $total     = $site->huts->count();
          $available = $site->huts->where('status', 'available')->count();
          $inUse     = $site->huts->where('status', 'in_use')->count();
          $damaged   = $site->huts->where('status', 'damaged')->count();
        @endphp
        <div class="d-flex gap-2 flex-wrap mb-3">
          <span class="badge bg-secondary">{{ $total }} cases</span>
          @if($available) <span class="badge bg-success">{{ $available }} dispos</span> @endif
          @if($inUse)     <span class="badge" style="background:var(--airid-red)">{{ $inUse }} utilisées</span> @endif
          @if($damaged)   <span class="badge bg-warning text-dark">{{ $damaged }} endommagées</span> @endif
        </div>

        <div class="d-flex gap-2">
          <a href="{{ route('sites.show', $site) }}" class="btn btn-sm btn-outline-primary flex-fill">
            <i class="fa-solid fa-eye me-1"></i>Voir
          </a>
          <a href="{{ route('sites.edit', $site) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-pen"></i>
          </a>
          @if($site->huts_count === 0)
          <form action="{{ route('sites.destroy', $site) }}" method="POST" onsubmit="return confirm('Supprimer ce site ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
          </form>
          @endif
        </div>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card text-center py-5">
      <div class="card-body">
        <i class="fa-solid fa-location-dot fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">Aucun site enregistré</h5>
        <a href="{{ route('sites.create') }}" class="btn btn-primary mt-2">
          <i class="fa-solid fa-plus me-1"></i> Créer le premier site
        </a>
      </div>
    </div>
  </div>
  @endforelse
</div>
@endsection
