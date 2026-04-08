@extends('layouts.app')
@section('title', 'Modifier – '.$site->name)

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-pen me-2 text-primary"></i>Modifier le site</span>
        <a href="{{ route('sites.show', $site) }}" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-arrow-left me-1"></i>Retour
        </a>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('sites.update', $site) }}" enctype="multipart/form-data">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nom du site <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $site->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Village</label>
              <input type="text" name="village" class="form-control" value="{{ old('village', $site->village) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Ville</label>
              <input type="text" name="city" class="form-control" value="{{ old('city', $site->city) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Latitude GPS</label>
              <input type="number" step="any" name="latitude" id="lat" class="form-control" value="{{ old('latitude', $site->latitude) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Longitude GPS</label>
              <input type="number" step="any" name="longitude" id="lng" class="form-control" value="{{ old('longitude', $site->longitude) }}">
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
                <option value="active"    {{ old('status', $site->status)==='active'    ? 'selected' : '' }}>En service</option>
                <option value="abandoned" {{ old('status', $site->status)==='abandoned' ? 'selected' : '' }}>Abandonné</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nouvelle image</label>
              @if($site->image_path)
                <div class="mb-2">
                  <img src="{{ asset('storage/'.$site->image_path) }}" alt="Image actuelle" style="height:60px;border-radius:6px;object-fit:cover">
                  <small class="d-block text-muted">Image actuelle</small>
                </div>
              @endif
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control" rows="3">{{ old('notes', $site->notes) }}</textarea>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer</button>
            <a href="{{ route('sites.show', $site) }}" class="btn btn-outline-secondary">Annuler</a>
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
