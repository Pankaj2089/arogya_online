@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Bed Distribution Reports</h3>
        <p class="text-subtitle text-muted">Search for Bed Distribution across OPD records.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/opd-reports') }}">Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bed Distribution Reports</li>
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


    @if(isset($records) && $records !== null)
    @if($records->count() > 0)
    <div class="mb-2">
      <a href="{{ url('/admin/bed-distribution-reports-export') }}?from_date={{ urlencode(request('from_date', '')) }}" class="btn btn-success btn-sm">
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
                <th>Department</th>
                <th>Gender</th>
                <th>Total Bed</th>
                <th>Bed No.</th>
            </tr>
            </thead>
           

            <tbody>
            @if($records->count() > 0)
                @foreach($records as $key => $row)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $row->department }}</td>
                    <td>{{ $row->gender == "M" ? "Male" : "Female" }}</td>
                    <td>{{ $row->total_bed }}</td>
                    <td>{{ $row->bed_numbers }}</td>
                </tr>
                @endforeach
            @else
            <tr>
                <td colspan="5" class="text-center">No records found.</td>
            </tr>
            @endif
            </tbody>
          </table>
        </div>
       
      </div>
    </div>
    @elseif(request()->filled('from_date'))
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
  $('#from_date').datepicker({
    dateFormat: 'mm/dd/yy',
    maxDate: 0
  });
});
</script>
@endsection
