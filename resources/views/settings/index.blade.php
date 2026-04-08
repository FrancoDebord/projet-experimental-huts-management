@extends('layouts.app')
@section('title', 'Paramètres — Notifications')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header fw-bold">
        <i class="fa-solid fa-bell me-2 text-primary"></i>Préférences de notification
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('settings.update') }}">
          @csrf

          <h6 class="fw-bold mb-3 text-muted">Notifications in-app</h6>

          <div class="mb-3 d-flex justify-content-between align-items-center p-3 rounded bg-light">
            <div>
              <div class="fw-semibold small">Incidents signalés</div>
              <div class="text-muted" style="font-size:.8rem">Recevoir une notification lorsqu'un incident est reporté</div>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="notify_incidents" value="1" id="notif_incidents"
                     {{ $prefs->notify_incidents ? 'checked' : '' }}>
            </div>
          </div>

          <div class="mb-3 d-flex justify-content-between align-items-center p-3 rounded bg-light">
            <div>
              <div class="fw-semibold small">Démarrage d'activité</div>
              <div class="text-muted" style="font-size:.8rem">Notifier au démarrage d'une session en cases expérimentales</div>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="notify_activity_start" value="1" id="notif_start"
                     {{ $prefs->notify_activity_start ? 'checked' : '' }}>
            </div>
          </div>

          <div class="mb-3 d-flex justify-content-between align-items-center p-3 rounded bg-light">
            <div>
              <div class="fw-semibold small">Fin d'activité</div>
              <div class="text-muted" style="font-size:.8rem">Notifier à la fin d'une session en cases expérimentales</div>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="notify_activity_end" value="1" id="notif_end"
                     {{ $prefs->notify_activity_end ? 'checked' : '' }}>
            </div>
          </div>

          <hr class="my-4">
          <h6 class="fw-bold mb-3 text-muted">Notifications PUSH (navigateur)</h6>

          <div class="mb-3 p-3 rounded" style="background:rgba(204,0,0,0.06);border:1px solid rgba(204,0,0,0.15)">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold small">Notifications PUSH</div>
                <div class="text-muted" style="font-size:.8rem">Recevoir des alertes même quand l'onglet est en arrière-plan</div>
              </div>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" name="push_enabled" value="1" id="push_enabled"
                       {{ $prefs->push_enabled ? 'checked' : '' }}>
              </div>
            </div>
            <div id="pushStatus" class="mt-2 small text-muted"></div>
            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="enablePushBtn">
              <i class="fa-solid fa-bell me-1"></i>Activer les notifications PUSH
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger mt-2 d-none" id="disablePushBtn">
              <i class="fa-solid fa-bell-slash me-1"></i>Désactiver les PUSH
            </button>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4">
              <i class="fa-solid fa-floppy-disk me-1"></i>Enregistrer mes préférences
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- User info --}}
    <div class="card">
      <div class="card-header fw-bold"><i class="fa-solid fa-user me-2 text-primary"></i>Mon compte</div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-5 text-muted">Nom</dt><dd class="col-7">{{ $user->name }}</dd>
          <dt class="col-5 text-muted">Email</dt><dd class="col-7">{{ $user->email }}</dd>
          <dt class="col-5 text-muted">Rôle</dt><dd class="col-7"><span class="badge bg-dark">{{ $user->role }}</span></dd>
          <dt class="col-5 text-muted">Téléphone</dt><dd class="col-7">{{ $user->telephone ?: '—' }}</dd>
        </dl>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const pushStatus = document.getElementById('pushStatus');
const enableBtn  = document.getElementById('enablePushBtn');
const disableBtn = document.getElementById('disablePushBtn');
const pushCheck  = document.getElementById('push_enabled');

async function checkPushStatus() {
  if (!('Notification' in window) || !('serviceWorker' in navigator)) {
    pushStatus.textContent = 'Les notifications PUSH ne sont pas supportées par ce navigateur.';
    enableBtn.classList.add('d-none');
    return;
  }
  const perm = Notification.permission;
  if (perm === 'granted') {
    pushStatus.innerHTML = '<span class="text-success"><i class="fa-solid fa-check me-1"></i>Notifications autorisées</span>';
    enableBtn.classList.add('d-none');
    disableBtn.classList.remove('d-none');
  } else if (perm === 'denied') {
    pushStatus.innerHTML = '<span class="text-danger">Notifications bloquées par le navigateur. Modifiez les paramètres du site.</span>';
    enableBtn.classList.add('d-none');
  }
}

enableBtn.addEventListener('click', async function() {
  const perm = await Notification.requestPermission();
  if (perm === 'granted') {
    pushCheck.checked = true;
    // Send subscription to server
    const reg = await navigator.serviceWorker.getRegistration('/');
    if (reg) {
      // Store simple subscription flag (no VAPID for now)
      await fetch('{{ route('notifications.push-subscribe') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ endpoint: location.origin + '/push-simple' })
      });
      // Enable browser notifications for foreground
      showToast('Notifications PUSH activées !', 'success');
    }
    checkPushStatus();
  } else {
    pushStatus.innerHTML = '<span class="text-danger">Permission refusée.</span>';
  }
});

disableBtn.addEventListener('click', async function() {
  pushCheck.checked = false;
  await fetch('{{ route('notifications.push-unsubscribe') }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  });
  disableBtn.classList.add('d-none');
  enableBtn.classList.remove('d-none');
  pushStatus.textContent = 'Notifications PUSH désactivées.';
});

function showToast(msg, type) {
  const t = document.createElement('div');
  t.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
  t.style.zIndex = 9999;
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3000);
}

checkPushStatus();
</script>
@endpush
