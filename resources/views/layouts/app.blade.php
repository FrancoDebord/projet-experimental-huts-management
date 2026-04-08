<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'AIRID Huts Manager') — {{ config('app.name') }}</title>

<!-- PWA -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#CC0000">
<link rel="apple-touch-icon" href="/storage/logo/airid.png">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome 6 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

<!-- AIRID Custom CSS -->
<link href="/css/airid.css" rel="stylesheet">
@stack('styles')
</head>
<body>

<!-- Sidebar overlay (mobile) -->
<div id="sidebar-overlay"></div>

<!-- ========== SIDEBAR ========== -->
<nav id="sidebar">
  <div class="sidebar-brand">
    <img src="/storage/logo/airid.png" alt="AIRID">
    <h6>Cases Expérimentales</h6>
  </div>

  <ul class="nav flex-column py-2 flex-grow-1" style="overflow-y:auto;">
    <li class="nav-section">Navigation</li>

    <li class="nav-item">
      <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i> Tableau de bord
      </a>
    </li>

    <li class="nav-section mt-2">Gestion</li>

    <li class="nav-item">
      <a href="{{ route('sites.index') }}" class="nav-link {{ request()->routeIs('sites.*') ? 'active' : '' }}">
        <i class="fa-solid fa-location-dot"></i> Sites
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('huts.index') }}" class="nav-link {{ request()->routeIs('huts.*') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i> Cases
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
        <i class="fa-solid fa-flask"></i> Projets
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('incidents.index') }}" class="nav-link {{ request()->routeIs('incidents.*') ? 'active' : '' }}">
        <i class="fa-solid fa-triangle-exclamation"></i> Incidents
      </a>
    </li>

    <li class="nav-section mt-2">Outils</li>

    <li class="nav-item">
      <a href="{{ route('availability.index') }}" class="nav-link {{ request()->routeIs('availability.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-check"></i> Disponibilité
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('maps.index') }}" class="nav-link {{ request()->routeIs('maps.*') ? 'active' : '' }}">
        <i class="fa-solid fa-map-location-dot"></i> Carte
      </a>
    </li>

    <li class="nav-section mt-2">Utilisateur</li>

    <li class="nav-item">
      <a href="{{ route('sleepers.index') }}" class="nav-link {{ request()->routeIs('sleepers.*') ? 'active' : '' }}">
        <i class="fa-solid fa-bed"></i> Dormeurs
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
        <i class="fa-solid fa-bell"></i> Notifications
        <span class="badge rounded-pill ms-auto" style="background:var(--airid-red);font-size:.6rem;display:none" id="sidebarNotifBadge"></span>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
        <i class="fa-solid fa-gear"></i> Paramètres
      </a>
    </li>
  </ul>

  <div id="sidebar-footer">
    <div class="user-info">
      <div class="avatar">{{ strtoupper(substr(auth()->user()->prenom ?? 'U', 0, 1)) }}</div>
      <div>
        <div class="user-name">{{ auth()->user()->name }}</div>
        <div class="user-role">{{ auth()->user()->role }}</div>
      </div>
    </div>
    <form action="{{ route('logout') }}" method="POST" class="mt-2">
      @csrf
      <button type="submit" class="btn btn-sm btn-outline-danger w-100">
        <i class="fa-solid fa-right-from-bracket me-1"></i> Déconnexion
      </button>
    </form>
  </div>
</nav>

<!-- ========== MAIN ========== -->
<div id="main-wrapper">
  <!-- Topbar -->
  <div id="topbar">
    <div class="d-flex align-items-center gap-2">
      <button id="sidebar-toggle"><i class="fa-solid fa-bars"></i></button>
      <h5 id="page-title" class="page-title mb-0">@yield('title', 'Dashboard')</h5>
    </div>
    <div class="d-flex align-items-center gap-2">
      @if(isset($breadcrumbs))
      <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb mb-0 small">
          @foreach($breadcrumbs as $bc)
            @if(!$loop->last)
              <li class="breadcrumb-item"><a href="{{ $bc['url'] }}">{{ $bc['label'] }}</a></li>
            @else
              <li class="breadcrumb-item active">{{ $bc['label'] }}</li>
            @endif
          @endforeach
        </ol>
      </nav>
      @endif
      {{-- Notification bell --}}
      <div class="dropdown">
        <button class="btn btn-link text-dark position-relative p-1" id="notifBell"
                data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none">
          <i class="fa-solid fa-bell fa-lg"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                style="background:var(--airid-red);font-size:.6rem;display:none" id="notifBadge"></span>
        </button>
        <div class="dropdown-menu dropdown-menu-end shadow p-0" style="width:340px;max-height:400px;overflow-y:auto" id="notifDropdownMenu">
          <div class="dropdown-header d-flex justify-content-between align-items-center border-bottom px-3 py-2">
            <span class="fw-bold">Notifications</span>
            <a href="{{ route('notifications.index') }}" class="small text-primary">Voir tout</a>
          </div>
          <div id="notifList">
            <div class="text-center py-3 text-muted small">Chargement…</div>
          </div>
        </div>
      </div>

      <span class="badge" style="background:var(--airid-red)">
        {{ now()->translatedFormat('d M Y') }}
      </span>
    </div>
  </div>

  <!-- Content -->
  <div id="content">
    {{-- Flash messages --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-xmark me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        <ul class="mb-0 mt-1">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @yield('content')
  </div>
</div>

<!-- ========== SCRIPTS ========== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Sidebar toggle
document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebar-overlay').classList.toggle('show');
});
document.getElementById('sidebar-overlay')?.addEventListener('click', function() {
  document.getElementById('sidebar').classList.remove('open');
  this.classList.remove('show');
});

// Auto-dismiss alerts after 5s
setTimeout(() => {
  document.querySelectorAll('.alert').forEach(el => {
    const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
    bsAlert.close();
  });
}, 5000);

// PWA service worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(() => {});
}

// ── Notification bell ──────────────────────────────────────────
async function refreshNotifBadge() {
  try {
    const r = await fetch('{{ route('notifications.unread-count') }}', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const d = await r.json();
    const n = d.count || 0;
    ['notifBadge', 'sidebarNotifBadge'].forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      if (n > 0) { el.textContent = n > 99 ? '99+' : n; el.style.display = ''; }
      else        { el.style.display = 'none'; }
    });
  } catch (e) {}
}

async function loadNotifDropdown() {
  try {
    const r   = await fetch('{{ route('notifications.latest') }}', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const items = await r.json();
    const list  = document.getElementById('notifList');
    if (!items.length) {
      list.innerHTML = '<div class="text-center py-3 text-muted small">Aucune notification</div>';
      return;
    }
    const notifUrl = '{{ route('notifications.index') }}';
    list.innerHTML = items.map(n => `
      <a href="${n.url || notifUrl}" class="dropdown-item py-2 border-bottom ${n.read ? '' : 'bg-light'}" style="white-space:normal">
        <div class="d-flex gap-2 align-items-start">
          <i class="fa-solid ${n.icon || 'fa-bell'} mt-1 small text-primary flex-shrink-0"></i>
          <div class="flex-grow-1">
            <div class="fw-semibold" style="font-size:.8rem">${n.title}</div>
            <div class="text-muted" style="font-size:.75rem">${n.message}</div>
            <div class="text-muted" style="font-size:.7rem">${n.created_at}</div>
          </div>
          ${!n.read ? '<span class="badge rounded-pill flex-shrink-0" style="background:var(--airid-red);font-size:.6rem">Nouveau</span>' : ''}
        </div>
      </a>`).join('');
  } catch (e) {}
}

document.getElementById('notifBell')?.addEventListener('show.bs.dropdown', loadNotifDropdown);
refreshNotifBadge();
setInterval(refreshNotifBadge, 60000);
</script>
@stack('scripts')
</body>
</html>
