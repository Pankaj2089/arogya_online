@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>OPD Statistics</h3>
        <p class="text-subtitle text-muted">Filter by year and month to view OPD Statistics.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/opd-reports') }}">Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">OPD Reports</li>
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
        <form id="opdReportsForm" method="post" action="{{ url('/admin/opd-statistics') }}">
    @csrf

    @php
        $currentYear = date('Y');

        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    @endphp

    <div class="row align-items-end">
        {{-- Year Dropdown --}}
        <div class="col-12 col-md-2">
            <div class="form-group mb-3">
                <label>Year</label>
                <select class="form-select" name="year" id="year">
                    <option value="">--Select Year--</option>

                    @for($i = $currentYear; $i >= $currentYear - 9; $i--)
                        <option value="{{ $i }}"
                            {{ old('year', $selectedYear ?? '') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Month Dropdown --}}
        <div class="col-12 col-md-2">
            <div class="form-group mb-3">
                <label>Month</label>
                <select class="form-select" name="month" id="month">
                    <option value="">--Select Month--</option>

                    @foreach($months as $num => $name)
                        <option value="{{ $num }}"
                            {{ old('month', $selectedMonth ?? '') == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Submit --}}
        <div class="col-12 col-md-1">
            <button type="submit" class="btn btn-primary mb-3">
                Submit
            </button>
        </div>

    </div>

    {{-- Error --}}
    @if(isset($filterError) && $filterError)
        <div class="text-danger mb-2">{{ $filterError }}</div>
    @endif

</form>
      </div>
    </div>

    @if(isset($datewiseData) && count($datewiseData) > 0)
      <div class="mb-2">
        <a href="{{ url('/admin/opd-statistics-export') }}?year={{ urlencode($selectedYear ?? '') }}&month={{ urlencode($selectedMonth ?? '') }}" class="btn btn-success btn-sm">
          <i class="bi bi-file-earmark-excel"></i> Export (CSV)
        </a>
      </div>
      
      <div class="card mt-3">
       
        <div class="card-body">
          {{-- Department Summary Table --}}
          <div class="table-responsive">
              <table class="table table-bordered table-striped">
    <thead class="bg-primary text-white">
        <tr>
            <th>#</th>
            <th>Department</th>

            <th>New Male</th>
            <th>New Female</th>
            <th>New Total</th>

            <th>Old Male</th>
            <th>Old Female</th>
            <th>Old Total</th>

            <th>Grand Total</th>
        </tr>
    </thead>

    <tbody>

        @php
            $totalNewMale = 0;
            $totalNewFemale = 0;
            $totalOldMale = 0;
            $totalOldFemale = 0;
        @endphp

        @foreach($departmentWise as $index => $row)

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
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->department }}</td>

                <td>{{ $row->new_male }}</td>
                <td>{{ $row->new_female }}</td>
                <td>{{ $newTotal }}</td>

                <td>{{ $row->old_male }}</td>
                <td>{{ $row->old_female }}</td>
                <td>{{ $oldTotal }}</td>

                <td><strong>{{ $grandTotal }}</strong></td>
            </tr>

        @endforeach

        {{-- GRAND TOTAL --}}
        @php
            $finalNewTotal = $totalNewMale + $totalNewFemale;
            $finalOldTotal = $totalOldMale + $totalOldFemale;
            $finalGrandTotal = $finalNewTotal + $finalOldTotal;
        @endphp

        <tr class="font-weight-bold bg-light">
            <td colspan="2" class="text-center"><strong>Total</strong></td>

            <td>{{ $totalNewMale }}</td>
            <td>{{ $totalNewFemale }}</td>
            <td>{{ $finalNewTotal }}</td>

            <td>{{ $totalOldMale }}</td>
            <td>{{ $totalOldFemale }}</td>
            <td>{{ $finalOldTotal }}</td>

            <td><strong>{{ $finalGrandTotal }}</strong></td>
        </tr>

    </tbody>
</table>
          </div>

        </div>
      </div>
     
    @elseif(isset($datewiseData) && count($datewiseData) === 0 && isset($fromDate))
    <div class="card mt-3">
      <div class="card-body">
        <p class="text-muted mb-0">No OPD records found for the selected date range and department.</p>
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
  $('#opdReportsForm').on('submit', function(){
    if ($.trim($('#year').val()) === '' || $.trim($('#month').val()) === '') {
      return false;
    }
  });
});
</script>
@endsection
