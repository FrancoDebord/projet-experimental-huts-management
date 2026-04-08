@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')
{{-- ===== STAT CARDS ===== --}}
<div class="row g-3 mb-4">
  @php
  $cards = [
    ['icon'=>'fa-location-dot',     'value'=>$stats['sites_total'],    'label'=>'Sites',             'sub'=>$stats['sites_active'].' actifs',       'color'=>'#CC0000'],
    ['icon'=>'fa-house',            'value'=>$stats['huts_total'],     'label'=>'Cases totales',      'sub'=>$stats['huts_available'].' disponibles', 'color'=>'#198754'],
    ['icon'=>'fa-circle-play',      'value'=>$stats['huts_in_use'],    'label'=>'En utilisation',     'sub'=>'aujourd\'hui',                         'color'=>'#CC0000'],
    ['icon'=>'fa-triangle-exclamation','value'=>$stats['incidents_open'],'label'=>'Incidents ouverts','sub'=>'à traiter',                            'color'=>'#ffc107'],
  ];
  @endphp
  @foreach($cards as $c)
  <div class="col-sm-6 col-xl-3">
    <div class="card stat-card h-100" style="border-left-color: {{ $c['color'] }}">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon" style="background: {{ $c['color'] }}22; color: {{ $c['color'] }}">
          <i class="fa-solid {{ $c['icon'] }}"></i>
        </div>
        <div>
          <div class="stat-value">{{ $c['value'] }}</div>
          <div class="stat-label">{{ $c['label'] }}</div>
          <small class="text-muted">{{ $c['sub'] }}</small>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ===== CHARTS ROW ===== --}}
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="fa-solid fa-chart-pie text-primary"></i>
        État des cases
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <canvas id="hutStatusChart" height="220"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="fa-solid fa-chart-bar text-primary"></i>
        Utilisations par mois (6 derniers mois)
      </div>
      <div class="card-body">
        <canvas id="usagesChart" height="120"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- ===== ACTIVE USAGES ===== --}}
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-circle-play text-danger me-2"></i>Utilisations en cours</span>
        <span class="badge bg-danger">{{ $activeUsages->count() }}</span>
      </div>
      <div class="card-body p-0">
        @if($activeUsages->isEmpty())
          <div class="text-center py-4 text-muted"><i class="fa-solid fa-inbox fa-2x mb-2"></i><br>Aucune utilisation en cours</div>
        @else
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead><tr><th>Case</th><th>Projet</th><th>Phase</th><th>Fin</th><th>Avancement</th></tr></thead>
              <tbody>
              @foreach($activeUsages as $u)
              <tr>
                <td><a href="{{ route('huts.show', $u->hut) }}" class="fw-semibold">{{ $u->hut->name }}</a></td>
                <td><span class="badge" style="background:var(--airid-red)">{{ $u->project?->project_code }}</span></td>
                <td><small class="text-muted">{{ $u->phase_name ?: '—' }}</small></td>
                <td><small>{{ $u->date_end->format('d/m/Y') }}</small></td>
                <td style="min-width:80px">
                  <div class="progress"><div class="progress-bar" style="width:{{ $u->progress_percent }}%"></div></div>
                  <small class="text-muted">{{ $u->progress_percent }}%</small>
                </td>
              </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-calendar-days text-info me-2"></i>Prochaines utilisations (30j)</span>
        <span class="badge bg-info">{{ $upcomingUsages->count() }}</span>
      </div>
      <div class="card-body p-0">
        @if($upcomingUsages->isEmpty())
          <div class="text-center py-4 text-muted"><i class="fa-solid fa-calendar-xmark fa-2x mb-2"></i><br>Aucune utilisation prévue</div>
        @else
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead><tr><th>Case</th><th>Projet</th><th>Début</th><th>Fin</th><th>Durée</th></tr></thead>
              <tbody>
              @foreach($upcomingUsages as $u)
              <tr>
                <td><a href="{{ route('huts.show', $u->hut) }}" class="fw-semibold">{{ $u->hut->name }}</a></td>
                <td><span class="badge bg-info text-dark">{{ $u->project?->project_code }}</span></td>
                <td><small>{{ $u->date_start->format('d/m/Y') }}</small></td>
                <td><small>{{ $u->date_end->format('d/m/Y') }}</small></td>
                <td><small class="text-muted">{{ $u->duration_in_days }}j</small></td>
              </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- ===== RECENT INCIDENTS ===== --}}
@if($recentIncidents->isNotEmpty())
<div class="card mb-4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Incidents récents</span>
    <a href="{{ route('incidents.index') }}" class="btn btn-sm btn-outline-primary">Tous les incidents</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr><th>Date</th><th>Case</th><th>Titre</th><th>Sévérité</th><th>Statut</th></tr></thead>
        <tbody>
        @foreach($recentIncidents as $inc)
        <tr>
          <td><small>{{ $inc->incident_date->format('d/m/Y') }}</small></td>
          <td>{{ $inc->hut?->name ?? '—' }}</td>
          <td><a href="{{ route('incidents.show', $inc) }}">{{ $inc->title }}</a></td>
          <td><span class="badge bg-{{ $inc->severity_color }}">{{ $inc->severity_label }}</span></td>
          <td><span class="badge bg-{{ $inc->status_color }}">{{ $inc->status_label }}</span></td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Donut chart: huts status
new Chart(document.getElementById('hutStatusChart'), {
  type: 'doughnut',
  data: {
    labels: {!! json_encode($hutsByStatus['labels']) !!},
    datasets: [{
      data: {!! json_encode($hutsByStatus['data']) !!},
      backgroundColor: {!! json_encode($hutsByStatus['colors']) !!},
      borderWidth: 2, borderColor: '#fff'
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom', labels: { font: { size: 11 } } }
    },
    cutout: '65%'
  }
});

// Bar chart: usages per month
new Chart(document.getElementById('usagesChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($monthLabels) !!},
    datasets: [{
      label: 'Utilisations',
      data: {!! json_encode($usagesPerMonth) !!},
      backgroundColor: 'rgba(204,0,0,0.7)',
      borderColor: '#CC0000',
      borderWidth: 1,
      borderRadius: 4
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 } }
    }
  }
});
</script>
@endpush
