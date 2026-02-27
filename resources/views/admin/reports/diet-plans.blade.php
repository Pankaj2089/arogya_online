@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Diet Plan Reports</h3>
        <p class="text-subtitle text-muted">Search for Diet Plan records by date.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Diet Plan Reports</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

  <section class="section">

    <div class="card">
      <div class="card-body">
        <form method="get" action="{{ url('/admin/diet-plan-reports') }}">
          <div class="row align-items-end">
            <div class="col-12 col-md-3">
              <div class="form-group mb-3">
                <label for="filter_date">Date</label>
                <input type="text"
                       class="form-control datepicker"
                       id="filter_date"
                       name="filter_date"
                       placeholder="MM/DD/YYYY"
                       value="{{ request('filter_date', '') }}"
                       autocomplete="off">
              </div>
            </div>

            <div class="col-12 col-md-2">
              <button type="submit"
                      class="btn btn-primary"
                      style="margin-top: -50px;">
                Submit
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    @if(isset($records) && $records !== null)

      @if($records->count() > 0)
      <div class="mb-2">
        <a href="{{ url('/admin/diet-plan-reports-export') }}?filter_date={{ urlencode(request('filter_date', '')) }}"
           class="btn btn-success btn-sm">
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
                  <th>Diet Date</th>
                  <th>IPD No.</th>
                  <th>OPD No.</th>
                  <th>Patient Name</th>
                  <th>Gender</th>
                  <th>Department</th>
                  <th>Morning</th>
                  <th>Afternoon</th>
                  <th>Evening</th>
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
                    <td>{{ date('d-m-Y', strtotime($row->plan_date)) ?? '—' }}</td>
                    <td>{{ $row->ipd_no ?? '—' }}</td>
                    <td>{{ $row->opd_no ?? '—' }}</td>
                    <td>{{ $row->patient_name ?? '—' }}</td>
                    <td>{{ $row->gendar ?? '—' }}</td>
                    <td>{{ $row->department->name ?? '—' }}</td>
                    <td>{{ $row->morning ?? 'No' }}</td>
                    <td>{{ $row->afternoon ?? 'No' }}</td>
                    <td>{{ $row->evening ?? 'No' }}</td>
                  </tr>

                  @endforeach
                @else
                  <tr>
                    <td colspan="10" class="text-center">No records found.</td>
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

    @elseif(request()->filled('filter_date'))
      <div class="card mt-3">
        <div class="card-body">
          <p class="text-muted mb-0">No records found for the selected date.</p>
        </div>
      </div>
    @endif

  </section>
</div>

<script>
$(document).ready(function(){
  $('#filter_date').datepicker({
    dateFormat: 'mm/dd/yy',
    maxDate: 0
  });
});
</script>

@endsection
