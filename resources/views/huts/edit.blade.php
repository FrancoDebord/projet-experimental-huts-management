@extends('layouts.app')
@section('title', 'Modifier – '.$hut->name)

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-pen me-2 text-primary"></i>Modifier la case — {{ $hut->name }}</span>
        <a href="{{ route('huts.show', $hut) }}" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-arrow-left me-1"></i>Retour
        </a>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('huts.update', $hut) }}" enctype="multipart/form-data">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-12">
              <div class="alert alert-light border py-2">
                <strong>Site :</strong> {{ $hut->site->name }} &nbsp;|&nbsp;
                <strong>Numéro :</strong> {{ $hut->number }} &nbsp;|&nbsp;
                <strong>Nom :</strong> {{ $hut->name }}
                <small class="text-muted d-block">Le site, numéro et nom ne peuvent pas être modifiés.</small>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Latitude GPS</label>
              <input type="number" step="any" name="latitude" id="lat" class="form-control" value="{{ old('latitude', $hut->latitude) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Longitude GPS</label>
              <input type="number" step="any" name="longitude" id="lng" class="form-control" value="{{ old('longitude', $hut->longitude) }}">
            </div>
            <div class="col-12">
              <button type="button" class="btn btn-sm btn-outline-secondary" id="gpsBtn" onclick="captureGPS()">
                <i class="fa-solid fa-location-crosshairs me-1"></i>Capturer ma position GPS
              </button>
              <span id="gpsStatus" class="ms-2 small text-muted"></span>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Nouvelle image</label>
              @if($hut->image_path)
                <div class="mb-2">
                  <img src="{{ asset('storage/'.$hut->image_path) }}" alt="" style="height:60px;border-radius:6px;object-fit:cover">
                  <small class="d-block text-muted">Image actuelle</small>
                </div>
              @endif
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control" rows="3">{{ old('notes', $hut->notes) }}</textarea>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer</button>
            <a href="{{ route('huts.show', $hut) }}" class="btn btn-outline-secondary">Annuler</a>
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
