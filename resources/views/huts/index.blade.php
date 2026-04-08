@extends('layouts.app')
@section('title', 'Cases expérimentales')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="fw-bold mb-0">Cases expérimentales</h5>
    <small class="text-muted">{{ $huts->count() }} case(s) — <span class="text-success">{{ $huts->where('status','available')->count() }} disponibles</span></small>
  </div>
  <a href="{{ route('huts.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Nouvelle case</a>
</div>

{{-- Quick filter pills --}}
<div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
  <span class="text-muted small">Filtrer :</span>
  @foreach($sites as $site)
  <button class="btn btn-sm btn-outline-secondary site-filter" data-site="{{ $site->id }}">
    {{ $site->name }} ({{ $site->huts->count() }})
  </button>
  @endforeach
  <button class="btn btn-sm btn-success status-filter" data-status="available">Disponibles</button>
  <button class="btn btn-sm" style="background:var(--airid-red);color:#fff" data-bs-toggle="class" class="status-filter" data-status="in_use">En utilisation</button>
  <button class="btn btn-sm btn-warning status-filter" data-status="damaged">Endommagées</button>
  <button class="btn btn-sm btn-outline-secondary" id="clearFilters">Tout afficher</button>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table id="hutsTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead>
          <tr>
            <th>Site</th>
            <th>N°</th>
            <th>Nom</th>
            <th>État</th>
            <th>Utilisation actuelle</th>
            <th>GPS</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($huts as $hut)
          @php $cur = $hut->currentUsage(); @endphp
          <tr data-site="{{ $hut->site_id }}" data-status="{{ $hut->status }}">
            <td>
              <a href="{{ route('sites.show', $hut->site) }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-location-dot me-1"></i>{{ $hut->site->name }}
              </a>
            </td>
            <td class="fw-bold text-center">{{ $hut->number }}</td>
            <td>
              <a href="{{ route('huts.show', $hut) }}" class="fw-semibold text-decoration-none">
                {{ $hut->name }}
              </a>
            </td>
            <td>{!! $hut->status_badge !!}</td>
            <td>
              @if($cur)
                <span class="badge" style="background:var(--airid-red)">{{ $cur->project?->project_code }}</span>
                @if($cur->phase_name)<small class="text-muted ms-1">{{ $cur->phase_name }}</small>@endif
                <div class="progress mt-1" style="height:4px">
                  <div class="progress-bar" style="width:{{ $cur->progress_percent }}%"></div>
                </div>
                <small class="text-muted">{{ $cur->days_remaining }}j restants</small>
              @else
                <span class="text-muted small">—</span>
              @endif
            </td>
            <td>
              @if($hut->hasCoordinates())
                <span class="text-success small"><i class="fa-solid fa-crosshairs me-1"></i>Oui</span>
              @else
                <span class="text-muted small">Non</span>
              @endif
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('huts.show', $hut) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <a href="{{ route('huts.edit', $hut) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                  <i class="fa-solid fa-pen"></i>
                </a>
                @if(!$hut->trashed())
                <form action="{{ route('huts.destroy', $hut) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Supprimer cette case ?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  const table = $('#hutsTable').DataTable({
    pageLength: 25,
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json'
    },
    order: [[0, 'asc'], [1, 'asc']],
    columnDefs: [{ orderable: false, targets: [6] }]
  });

  // Site filter buttons
  $('.site-filter').on('click', function() {
    $('.site-filter').removeClass('active');
    $(this).addClass('active');
    const siteId = $(this).data('site');
    table.column(0).search('').draw(); // reset
    // Filter by site using row data attribute
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
      return $(table.row(dataIndex).node()).data('site') == siteId;
    });
    table.draw();
    $.fn.dataTable.ext.search.pop();
  });

  // Status filter
  let activeStatusFilter = null;
  $('.status-filter').on('click', function() {
    const status = $(this).data('status');
    if (activeStatusFilter === status) {
      activeStatusFilter = null;
      $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(f => f._statusFilter !== true);
      table.draw();
      $(this).removeClass('active');
    } else {
      activeStatusFilter = status;
      $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(f => f._statusFilter !== true);
      const fn = function(settings, data, dataIndex) {
        return $(table.row(dataIndex).node()).data('status') === status;
      };
      fn._statusFilter = true;
      $.fn.dataTable.ext.search.push(fn);
      table.draw();
      $('.status-filter').removeClass('active');
      $(this).addClass('active');
    }
  });

  $('#clearFilters').on('click', function() {
    $.fn.dataTable.ext.search = [];
    activeStatusFilter = null;
    table.search('').draw();
    $('.site-filter, .status-filter').removeClass('active');
  });
});
</script>
@endpush
