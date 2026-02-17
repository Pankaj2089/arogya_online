@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>IPD Reports</h3>
        <p class="text-subtitle text-muted">Filter by date range and department to view IPD reports.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/ipd-reports') }}">Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">IPD Reports</li>
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
        <form id="IPDReportsForm" method="post" action="{{ url('/admin/ipd-reports') }}">
          @csrf
          <div class="row align-items-end">
            <div class="col-12 col-md-3">
              <div class="form-group mb-3">
                <label for="from_date">From Date <span class="text-danger">*</span></label>
                <input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="MM/DD/YYYY" value="{{ old('from_date', $fromDate ?? '') }}" autocomplete="off">
              </div>
            </div>
            <div class="col-12 col-md-3">
              <div class="form-group mb-3">
                <label for="to_date">To Date <span class="text-danger">*</span></label>
                <input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="MM/DD/YYYY" value="{{ old('to_date', $toDate ?? '') }}" autocomplete="off">
              </div>
            </div>
            <div class="col-12 col-md-2">
              <button type="submit" class="btn btn-primary" style="margin-top: -50px;">Submit</button>
            </div>
          </div>
          @if(isset($filterError) && $filterError)
          <div class="text-danger mb-2">{{ $filterError }}</div>
          @endif
        </form>
      </div>
    </div>

    @if(isset($datewiseData) && count($datewiseData) > 0)
      <div class="mb-2">
        <a href="{{ url('/admin/ipd-reports-export') }}?from_date={{ urlencode($fromDate ?? '') }}&to_date={{ urlencode($toDate ?? '') }}&dept_id={{ $filterDeptId ?? '' }}" class="btn btn-success btn-sm">
          <i class="bi bi-file-earmark-excel"></i> Export (CSV)
        </a>
      </div>
      @foreach($datewiseData as $dateKey => $dayData)
      <div class="card mt-3">
        <div class="card-header">
          <h5 class="mb-0">Date: {{ \Carbon\Carbon::parse($dateKey)->format('d-m-Y') }}</h5>
        </div>
        <div class="card-body">
          {{-- IPD Details Table --}}
          <div class="table-responsive mb-4">
            <table class="table table-bordered mb-0">
              <thead class="thead-dark">
                <tr>
                  <th>S. No.</th>
                  <th>IPD No.</th>
                  <th>OPD No.</th>
                  <th>Patient Name</th>
                  <th>Age / Gender</th>
                  <th>Location</th>
                  <th>Department</th>
                  <th>Diagnosis</th>
                </tr>
              </thead>
              <tbody>
                @foreach($dayData->ipd_records as $idx => $IPD)
                <tr>
                  <td>{{ $idx + 1 }}</td>
                  <td>{{ $IPD->ipd_number ?? '—' }}</td>
                  <td>{{ $IPD->opd->opd_number ?? '—' }}</td>
                  <td>{{ $IPD->patient_name ?? '—' }}</td>
                  <td>{{ $IPD->patient_age ?? '—' }} / {{ $IPD->gender ?? '—' }}</td>
                  <td>{{ $IPD->address ?? '—' }}</td>
                  <td>{{ $IPD->opd->department ? $IPD->opd->department->name : '—' }}</td>
                  <td>{{ $IPD->diagnosis ? $IPD->diagnosis : '—' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Department Summary Table --}}
          <div class="table-responsive">
              <table class="table table-bordered mb-0">
                  <thead class="thead-dark">
                      <tr>
                          <th rowspan="2">S. No.</th>
                          <th rowspan="2">Department</th>

                          <th colspan="3" class="text-center">New Patient</th>
                          <th colspan="3" class="text-center">Old Patient</th>

                          <th rowspan="2">Grand Total</th>
                      </tr>
                      <tr>
                          <th>Male</th>
                          <th>Female</th>
                          <th>Total</th>

                          <th>Male</th>
                          <th>Female</th>
                          <th>Total</th>
                      </tr>
                  </thead>

                  <tbody>
                      @php
                          $totalNewMale = 0;
                          $totalNewFemale = 0;
                          $totalOldMale = 0;
                          $totalOldFemale = 0;
                      @endphp

                      @foreach($dayData->dept_summary as $dIdx => $row)

                          @php
                              $newTotal = $row->new_male + $row->new_female;
                              $oldTotal = $row->old_male + $row->old_female;
                              $grandTotal = $newTotal + $oldTotal;

                              $totalNewMale += $row->new_male;
                              $totalNewFemale += $row->new_female;
                              $totalOldMale += $row->old_male;
                              $totalOldFemale += $row->old_female;
                          @endphp

                          <tr>
                              <td>{{ $dIdx + 1 }}</td>
                              <td>{{ $row->department }}</td>

                              <td>{{ $row->new_male }}</td>
                              <td>{{ $row->new_female }}</td>
                              <td>{{ $newTotal }}</td>

                              <td>{{ $row->old_male }}</td>
                              <td>{{ $row->old_female }}</td>
                              <td>{{ $oldTotal }}</td>

                              <td>{{ $grandTotal }}</td>
                          </tr>
                      @endforeach

                      {{-- Grand Total Row --}}
                      @php
                          $finalNewTotal = $totalNewMale + $totalNewFemale;
                          $finalOldTotal = $totalOldMale + $totalOldFemale;
                          $finalGrandTotal = $finalNewTotal + $finalOldTotal;
                      @endphp

                      <tr class="font-weight-bold bg-light">
                          <td colspan="2" class="text-center">Total</td>

                          <td>{{ $totalNewMale }}</td>
                          <td>{{ $totalNewFemale }}</td>
                          <td>{{ $finalNewTotal }}</td>

                          <td>{{ $totalOldMale }}</td>
                          <td>{{ $totalOldFemale }}</td>
                          <td>{{ $finalOldTotal }}</td>

                          <td>{{ $finalGrandTotal }}</td>
                      </tr>
                  </tbody>
              </table>
          </div>

        </div>
      </div>
      @endforeach
    @elseif(isset($datewiseData) && count($datewiseData) === 0 && isset($fromDate))
    <div class="card mt-3">
      <div class="card-body">
        <p class="text-muted mb-0">No IPD records found for the selected date range and department.</p>
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
  $('#IPDReportsForm').on('submit', function(){
    if ($.trim($('#from_date').val()) === '' || $.trim($('#to_date').val()) === '') {
      return false;
    }
  });
});
</script>
@endsection
