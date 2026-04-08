@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="fw-bold mb-0">Mes notifications</h5>
  <form action="{{ route('notifications.read-all') }}" method="POST">
    @csrf
    <button class="btn btn-outline-secondary btn-sm">
      <i class="fa-solid fa-check-double me-1"></i>Tout marquer comme lu
    </button>
  </form>
</div>

@forelse($notifications as $notif)
<div class="card mb-2 {{ !$notif->is_read ? 'border-start border-4 border-primary' : '' }}">
  <div class="card-body py-2">
    <div class="d-flex align-items-start gap-3">
      <div class="mt-1">
        <i class="fa-solid {{ $notif->icon }} fa-lg"></i>
      </div>
      <div class="flex-grow-1">
        <div class="d-flex justify-content-between">
          <span class="fw-semibold small">{{ $notif->title }}</span>
          <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
        </div>
        <p class="mb-0 small text-muted mt-1">{{ $notif->message }}</p>
        @if($notif->url)
          <a href="{{ $notif->url }}" class="btn btn-xs btn-outline-primary mt-1 small">
            <i class="fa-solid fa-arrow-right me-1"></i>Voir le détail
          </a>
        @endif
      </div>
      @if(!$notif->is_read)
      <div>
        <span class="badge rounded-pill" style="background:var(--airid-red)">Nouveau</span>
      </div>
      @endif
    </div>
  </div>
</div>
@empty
<div class="card text-center py-5">
  <div class="card-body text-muted">
    <i class="fa-solid fa-bell-slash fa-3x mb-3"></i>
    <h5>Aucune notification</h5>
    <p class="small">Vous recevrez ici les alertes d'incidents et d'activités.</p>
    <a href="{{ route('settings.index') }}" class="btn btn-outline-primary btn-sm">
      <i class="fa-solid fa-gear me-1"></i>Configurer mes notifications
    </a>
  </div>
</div>
@endforelse

<div>{{ $notifications->links() }}</div>
@endsection
