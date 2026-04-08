@extends('layouts.app')
@section('title', 'Vérification de disponibilité')

@section('content')
<div class="row justify-content-center mb-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <i class="fa-solid fa-calendar-check me-2 text-primary"></i>
        Vérifier la disponibilité des cases
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('availability.check') }}" class="row g-3 align-items-end">
          @csrf
          <div class="col-md-4">
            <label class="form-label fw-semibold">Date de début <span class="text-danger">*</span></label>
            <input type="date" name="date_start" class="form-control"
                   value="{{ $start ?? '' }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Date de fin <span class="text-danger">*</span></label>
            <input type="date" name="date_end" class="form-control"
                   value="{{ $end ?? '' }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Site (optionnel)</label>
            <select name="site_id" class="form-select">
              <option value="">Tous les sites</option>
              @foreach($sites as $site)
                <option value="{{ $site->id }}" {{ ($siteId ?? null)===$site->id?'selected':'' }}>{{ $site->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary px-4">
              <i class="fa-solid fa-magnifying-glass me-1"></i>Vérifier la disponibilité
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@if(isset($results))
<div class="row mb-4">
  <div class="col-md-6">
    <div class="card border-success">
      <div class="card-body d-flex align-items-center gap-3">
        <i class="fa-solid fa-circle-check text-success fa-2x"></i>
        <div>
          <div class="fs-3 fw-bold text-success">{{ $available }}</div>
          <div class="text-muted small">Case(s) disponible(s)</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-danger">
      <div class="card-body d-flex align-items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-danger fa-2x"></i>
        <div>
          <div class="fs-3 fw-bold text-danger">{{ $unavailable }}</div>
          <div class="text-muted small">Case(s) non disponible(s)</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Period info --}}
<div class="alert alert-info mb-3">
  <i class="fa-solid fa-calendar me-2"></i>
  Période analysée : <strong>{{ \Carbon\Carbon::parse($start)->format('d/m/Y') }}</strong>
  au <strong>{{ \Carbon\Carbon::parse($end)->format('d/m/Y') }}</strong>
  ({{ \Carbon\Carbon::parse($start)->diffInDays($end) + 1 }} jours)
</div>

{{-- Group by site --}}
@foreach($results->groupBy(fn($r) => $r['hut']->site_id) as $siteId => $siteResults)
@php $site = $siteResults->first()['hut']->site; @endphp
<div class="card mb-3">
  <div class="card-header d-flex justify-content-between">
    <span><i class="fa-solid fa-location-dot me-2 text-primary"></i>{{ $site->name }}</span>
    <span>
      <span class="badge bg-success me-1">{{ $siteResults->where('available', true)->count() }} dispos</span>
      <span class="badge bg-danger">{{ $siteResults->where('available', false)->count() }} indispos</span>
    </span>
  </div>
  <div class="card-body">
    <div class="row g-2">
      @foreach($siteResults->sortBy(fn($r) => $r['hut']->number) as $result)
      @php $hut = $result['hut']; @endphp
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="avail-hut {{ $result['available'] ? 'avail' : 'unavail' }} flex-column align-items-start">
          <div class="fw-bold">
            <i class="fa-solid {{ $result['available'] ? 'fa-circle-check' : 'fa-circle-xmark' }} me-1"></i>
            Case {{ $hut->number }}
          </div>
          @if(!$result['available'] && $result['reason'])
            <small class="d-block mt-1">{{ $result['reason'] }}</small>
          @endif
          @if($result['available'])
            <small class="d-block mt-1 opacity-75">Disponible</small>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endforeach
@endif
@endsection
