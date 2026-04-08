@extends('layouts.app')
@section('title', 'Nouvelle case')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header"><i class="fa-solid fa-house me-2 text-primary"></i>Ajouter une case expérimentale</div>
      <div class="card-body">
        <form method="POST" action="{{ route('huts.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Site <span class="text-danger">*</span></label>
              <select name="site_id" id="siteSelect" class="form-select @error('site_id') is-invalid @enderror" required>
                <option value="">— Choisir un site —</option>
                @foreach($sites as $s)
                  <option value="{{ $s->id }}" {{ (old('site_id', request('site_id'))==$s->id)?'selected':'' }}
                          data-name="{{ $s->name }}">{{ $s->name }}</option>
                @endforeach
              </select>
              @error('site_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Numéro <span class="text-danger">*</span></label>
              <input type="number" name="number" min="1"
                     class="form-control @error('number') is-invalid @enderror"
                     value="{{ old('number') }}" required placeholder="1">
              @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <div class="alert alert-info py-2 small">
                <i class="fa-solid fa-circle-info me-1"></i>
                Le nom de la case sera automatiquement généré : <strong id="namePreview">…</strong>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Latitude GPS</label>
              <input type="number" step="any" name="latitude" id="lat" class="form-control" value="{{ old('latitude') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Longitude GPS</label>
              <input type="number" step="any" name="longitude" id="lng" class="form-control" value="{{ old('longitude') }}">
            </div>
            <div class="col-12">
              <button type="button" class="btn btn-sm btn-outline-secondary" id="gpsBtn" onclick="captureGPS()">
                <i class="fa-solid fa-location-crosshairs me-1"></i>Capturer ma position GPS
              </button>
              <span id="gpsStatus" class="ms-2 small text-muted"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">État initial</label>
              <select name="status" class="form-select">
                <option value="available" selected>Disponible</option>
                <option value="damaged">Endommagée</option>
                <option value="abandoned">Abandonnée</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i>Créer</button>
            <a href="{{ route('huts.index') }}" class="btn btn-outline-secondary">Annuler</a>
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

function updatePreview() {
  const sel = document.getElementById('siteSelect');
  const num = document.querySelector('[name="number"]').value;
  const name = sel.options[sel.selectedIndex]?.dataset?.name || '…';
  document.getElementById('namePreview').textContent = num ? `${name} Case ${num}` : '…';
}
document.getElementById('siteSelect')?.addEventListener('change', updatePreview);
document.querySelector('[name="number"]')?.addEventListener('input', updatePreview);
updatePreview();
</script>
@endpush
