@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>IPD</h3>
        <p class="text-subtitle text-muted">Filter by date range and department to view IPD reports.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/opd-reports') }}">Reports</a></li>
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

    @if($departmentWiseSummary->count()>0)

<div class="mb-2">
<a href="{{ url('/admin/ipd-statistics-export') }}?year={{$selectedYear}}&month={{$selectedMonth}}"
class="btn btn-success btn-sm">
Export (CSV)
</a>
</div>

<div class="card mt-3">
<div class="card-body">

<table class="table table-bordered">
<thead class="thead-dark">
<tr>
<th rowspan="2">S.No</th>
<th rowspan="2">Department</th>
<th colspan="3" class="text-center">New Patient</th>
<th colspan="3" class="text-center">Discharge Patient</th>
<th rowspan="2">Grand Total</th>
</tr>
<tr>
<th>Male</th><th>Female</th><th>Total</th>
<th>Male</th><th>Female</th><th>Total</th>
</tr>
</thead>

<tbody>
@php
$tNM=$tNF=$tOM=$tOF=0;
@endphp

@foreach($departmentWiseSummary as $i=>$row)

@php
$new=$row->new_male+$row->new_female;
$old=$row->old_male+$row->old_female;
$grand=$new+$old;

$tNM+=$row->new_male;
$tNF+=$row->new_female;
$tOM+=$row->old_male;
$tOF+=$row->old_female;
@endphp

<tr>
<td>{{$i+1}}</td>
<td>{{$row->department}}</td>

<td>{{$row->new_male}}</td>
<td>{{$row->new_female}}</td>
<td>{{$new}}</td>

<td>{{$row->old_male}}</td>
<td>{{$row->old_female}}</td>
<td>{{$old}}</td>

<td><b>{{$grand}}</b></td>
</tr>

@endforeach

<tr class="bg-light font-weight-bold">
<td colspan="2">Total</td>
<td>{{$tNM}}</td>
<td>{{$tNF}}</td>
<td>{{$tNM+$tNF}}</td>
<td>{{$tOM}}</td>
<td>{{$tOF}}</td>
<td>{{$tOM+$tOF}}</td>
<td>{{$tNM+$tNF+$tOM+$tOF}}</td>
</tr>

</tbody>
</table>

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
    if ($.trim($('#year').val()) === '' || $.trim($('#month').val()) === '') {
      return false;
    }
  });
});
</script>
@endsection
