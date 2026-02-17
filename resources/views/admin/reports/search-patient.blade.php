@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Search Patient</h3>
        <p class="text-subtitle text-muted">Search for patients across OPD records.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/opd-reports') }}">Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">Search Patient</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <section class="section">
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card">
      <div class="card-body">
        <form id="searchPatientForm" method="get" action="{{ url('/admin/search-patient') }}">
          <div class="row align-items-end">
            <div class="col-12 col-md-3">
              <div class="form-group mb-3">
                <label for="patient_name">Patient Name</label>
                <input type="text" class="form-control" id="patient_name" name="patient_name" placeholder="Patient Name" value="{{ request('patient_name', '') }}">
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="from_date">From Date</label>
                <input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="MM/DD/YYYY" value="{{ request('from_date', '') }}" autocomplete="off">
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="to_date">To Date</label>
                <input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="MM/DD/YYYY" value="{{ request('to_date', '') }}" autocomplete="off">
              </div>
            </div>
            <div class="col-12 col-md-2">
              <button type="submit" class="btn btn-primary" style="margin-top: -50px;">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    @if(isset($records) && $records !== null)
    @if($records->count() > 0)
    <div class="mb-2">
      <a href="{{ url('/admin/search-patient-export') }}?patient_name={{ urlencode(request('patient_name', '')) }}&from_date={{ urlencode(request('from_date', '')) }}&to_date={{ urlencode(request('to_date', '')) }}" class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-excel"></i> Export (CSV)
      </a>
    </div>
    @endif
    <div class="card mt-3">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered mb-0">
            <thead class="thead-dark">
              <tr>
                <th>S.No.</th>
                <th>OPD No.</th>
                <th>Patient Name</th>
                <th>Fath / Husb Name</th>
                <th>Age / Gender</th>
                <th>Category</th>
                <th>Mobile No.</th>
                <th>Address</th>
              </tr>
            </thead>
            <tbody>
              @if($records->count() > 0)
                @foreach($records as $key => $row)
                @php
                  $page = $records->currentPage();
                  $perPage = $records->perPage();
                  $sr = ($page - 1) * $perPage + $key + 1;
                @endphp
                <tr>
                  <td>{{ $sr }}</td>
                  <td>{{ $row->opd_number ?? '—' }}</td>
                  <td>{{ $row->patient_name ?? '—' }}</td>
                  <td>{{ $row->fath_husb_name ?? '—' }}</td>
                  <td>{{ $row->patient_age ?? '—' }} / {{ $row->gender ?? '—' }}</td>
                  <td>{{ $row->register_type ?? '—' }}</td>
                  <td>—</td>
                  <td>{{ $row->address ?? '—' }}</td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="8" class="text-center">No records found.</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
        @if($records->count() > 0)
        <div class="mt-3">
          {{ $records->appends(request()->except('page'))->links('vendor.pagination.custom') }}
        </div>
        @endif
      </div>
    </div>
    @elseif(request()->filled('patient_name') || request()->filled('from_date') || request()->filled('to_date'))
    <div class="card mt-3">
      <div class="card-body">
        <p class="text-muted mb-0">No records found for the selected criteria.</p>
      </div>
    </div>
    @endif
  </section>
</div>
<script>
$(document).ready(function(){
  $('#from_date, #to_date').datepicker({
    dateFormat: 'mm/dd/yy',
    maxDate: 0
  });
});
</script>
@endsection
