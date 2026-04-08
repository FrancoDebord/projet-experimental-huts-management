@extends('layouts.app')
@section('title', $site->name)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1">{{ $site->name }} {!! $site->status_badge !!}</h4>
    @if($site->village || $site->city)
      <p class="text-muted mb-0"><i class="fa-solid fa-map-pin me-1"></i>{{ implode(', ', array_filter([$site->village, $site->city])) }}</p>
    @endif
    @if($site->hasCoordinates())
      <small class="text-muted"><i class="fa-solid fa-crosshairs me-1"></i>{{ $site->latitude }}, {{ $site->longitude }}</small>
    @endif
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('sites.edit', $site) }}" class="btn btn-outline-primary"><i class="fa-solid fa-pen me-1"></i>Modifier</a>
    <a href="{{ route('huts.create') }}?site_id={{ $site->id }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Ajouter case</a>
  </div>
</div>

@if($site->image_path)
<div class="mb-4">
  <img src="{{ asset('storage/'.$site->image_path) }}" alt="{{ $site->name }}"
       style="max-height:280px;width:100%;object-fit:cover;border-radius:12px">
</div>
@endif

@if($site->notes)
<div class="alert alert-light border mb-4"><i class="fa-solid fa-circle-info me-2 text-primary"></i>{{ $site->notes }}</div>
@endif

{{-- Cases grid --}}
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="fa-solid fa-house me-2 text-primary"></i>Cases ({{ $site->huts->count() }})</span>
    <a href="{{ route('huts.create') }}?site_id={{ $site->id }}" class="btn btn-sm btn-primary">
      <i class="fa-solid fa-plus me-1"></i>Ajouter
    </a>
  </div>
  <div class="card-body">
    @if($site->huts->isEmpty())
      <div class="text-center py-4 text-muted">
        <i class="fa-solid fa-house fa-3x mb-2"></i><br>Aucune case enregistrée pour ce site
      </div>
    @else
      <div class="row g-2">
        @foreach($site->huts->sortBy('number') as $hut)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="{{ route('huts.show', $hut) }}" class="text-decoration-none">
            <div class="hut-card {{ $hut->status }}">
              <div class="hut-icon"><i class="fa-solid fa-house"></i></div>
              <div class="fw-bold small">Case {{ $hut->number }}</div>
              {!! $hut->status_badge !!}
              @php $cur = $hut->currentUsage(); @endphp
              @if($cur)
                <div class="mt-1"><small class="text-muted">{{ $cur->project?->project_code }}</small></div>
              @endif
            </div>
          </a>
        </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
