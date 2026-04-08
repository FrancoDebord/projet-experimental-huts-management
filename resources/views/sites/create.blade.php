@extends('layouts.app')
@section('title', 'Nouveau site')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header"><i class="fa-solid fa-location-dot me-2 text-primary"></i>Créer un nouveau site</div>
      <div class="card-body">
        <form method="POST" action="{{ route('sites.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nom du site <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name') }}" required placeholder="Ex: Site Vallée du Kou">
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Village</label>
              <input type="text" name="village" class="form-control" value="{{ old('village') }}" placeholder="Nom du village">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Ville</label>
              <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="Ville proche">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Latitude GPS</label>
              <input type="number" step="any" name="latitude" id="lat" class="form-control @error('latitude') is-invalid @enderror"
                     value="{{ old('latitude') }}" placeholder="Ex: 11.5524">
              @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Longitude GPS</label>
              <input type="number" step="any" name="longitude" id="lng" class="form-control @error('longitude') is-invalid @enderror"
                     value="{{ old('longitude') }}" placeholder="Ex: -4.2983">
              @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <button type="button" class="btn btn-sm btn-outline-secondary" id="gpsBtn" onclick="captureGPS()">
                <i class="fa-solid fa-location-crosshairs me-1"></i>Capturer ma position GPS
              </button>
              <span id="gpsStatus" class="ms-2 small text-muted"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Statut</label>
              <select name="status" class="form-select">
                <option value="active" {{ old('status','active')==='active'?'selected':'' }}>En service</option>
                <option value="abandoned" {{ old('status')==='abandoned'?'selected':'' }}>Abandonné</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Image du site</label>
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes / Description</label>
              <textarea name="notes" class="form-control" rows="3" placeholder="Informations complémentaires…">{{ old('notes') }}</textarea>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer</button>
            <a href="{{ route('sites.index') }}" class="btn btn-outline-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
function captureGPS() {
  const btn = document.getElementById('gpsBtn');
  const status = document.getElementById('gpsStatus');
  if (!navigator.geolocation) { status.textContent = 'Géolocalisation non supportée.'; return; }
  btn.disabled = true;
  status.textContent = 'Localisation en cours…';
  navigator.geolocation.getCurrentPosition(
    pos => {
      document.getElementById('lat').value = pos.coords.latitude.toFixed(6);
      document.getElementById('lng').value = pos.coords.longitude.toFixed(6);
      status.innerHTML = '<span class="text-success"><i class="fa-solid fa-check me-1"></i>Position capturée !</span>';
      btn.disabled = false;
    },
    err => {
      status.innerHTML = '<span class="text-danger">' + err.message + '</span>';
      btn.disabled = false;
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}
</script>
@endpush
