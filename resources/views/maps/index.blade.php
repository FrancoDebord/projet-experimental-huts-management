@extends('layouts.app')
@section('title', 'Carte des sites')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
#map { height: 620px; border-radius: 12px; }
.legend { background: white; padding: 10px 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); font-size: 13px; }
.legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.legend-dot { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0">Carte des sites et cases</h5>
  <div class="d-flex gap-2">
    <div class="legend">
      <div class="legend-item"><div class="legend-dot" style="background:#198754"></div>Disponible</div>
      <div class="legend-item"><div class="legend-dot" style="background:#CC0000"></div>En utilisation</div>
      <div class="legend-item"><div class="legend-dot" style="background:#ffc107"></div>Endommagée</div>
      <div class="legend-item"><div class="legend-dot" style="background:#6c757d"></div>Abandonnée</div>
    </div>
  </div>
</div>

<div id="map"></div>

<div class="row mt-3 g-3">
  @foreach($sites as $site)
  <div class="col-md-6 col-xl-4">
    <div class="card h-100">
      <div class="card-body py-2">
        <h6 class="fw-bold mb-1">
          <i class="fa-solid fa-location-dot me-1 text-primary"></i>{{ $site->name }}
          {!! $site->status_badge !!}
        </h6>
        @if($site->village || $site->city)
          <small class="text-muted">{{ implode(', ', array_filter([$site->village, $site->city])) }}</small>
        @endif
        <div class="d-flex flex-wrap gap-1 mt-2">
          @foreach($site->huts as $hut)
          <a href="{{ route('huts.show', $hut) }}"
             class="badge text-decoration-none badge-{{ $hut->status }}"
             title="{{ $hut->status_label }}">
            {{ $hut->number }}
          </a>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([12.36, -1.53], 7);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  maxZoom: 19
}).addTo(map);

const statusColors = {
  available:  '#198754',
  in_use:     '#CC0000',
  damaged:    '#ffc107',
  abandoned:  '#6c757d'
};

function makeIcon(color) {
  return L.divIcon({
    className: '',
    html: `<div style="width:24px;height:24px;background:${color};border:3px solid white;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,0.4)"></div>`,
    iconSize: [24, 24],
    iconAnchor: [12, 12]
  });
}

function makeSiteIcon() {
  return L.divIcon({
    className: '',
    html: `<div style="width:32px;height:32px;background:#1A1A1A;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:14px">S</div>`,
    iconSize: [32, 32],
    iconAnchor: [16, 16]
  });
}

// Load data from API
fetch('/api/maps/data')
  .then(r => r.json())
  .then(sites => {
    const bounds = [];

    sites.forEach(site => {
      if (site.lat && site.lng) {
        const marker = L.marker([site.lat, site.lng], { icon: makeSiteIcon() }).addTo(map);
        marker.bindPopup(`
          <b>${site.name}</b><br>
          <small>${site.village || ''} ${site.city || ''}</small><br>
          <small>${site.huts_count} case(s)</small><br>
          <a href="/sites/${site.id}" class="btn btn-sm btn-outline-primary mt-1" style="font-size:11px">Voir le site</a>
        `);
        bounds.push([site.lat, site.lng]);
      }

      site.huts.forEach(hut => {
        if (hut.lat && hut.lng) {
          const color  = statusColors[hut.status] || '#6c757d';
          const marker = L.marker([hut.lat, hut.lng], { icon: makeIcon(color) }).addTo(map);
          let popupHtml = `<b>${hut.name}</b><br><span class="badge" style="background:${color}">${hut.status}</span>`;
          if (hut.project) popupHtml += `<br><small>Projet: <b>${hut.project}</b>${hut.phase ? ' – '+hut.phase : ''}</small>`;
          popupHtml += `<br><a href="${hut.url}" style="font-size:11px">Voir la case →</a>`;
          marker.bindPopup(popupHtml);
          bounds.push([hut.lat, hut.lng]);
        }
      });
    });

    if (bounds.length > 1) {
      map.fitBounds(bounds, { padding: [40, 40] });
    }
  })
  .catch(() => {
    console.log('No GPS coordinates available yet');
  });
</script>
@endpush
