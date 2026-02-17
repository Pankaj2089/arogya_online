@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Diet Plans</h3>
        <p class="text-subtitle text-muted">Filter by date and department to assign diseases to ipd records.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Diet Plans</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <section class="section">
    <div class="card">
      <div class="card-body">
        <form id="filterForm" method="post" action="{{ url('/admin/diet-plan') }}">
          @csrf
          <div class="row align-items-end">
            <div class="col-12 col-md-3">
              <div class="form-group mb-3">
                <label for="filter_date">Date <span class="text-danger">*</span></label>
                <input type="text" class="form-control datepicker" id="filter_date" name="filter_date" placeholder="MM/DD/YYYY" value="{{ old('filter_date', $filterDate ?? '') }}" autocomplete="off">
              </div>
            </div>
            <div class="col-12 col-md-3">
              <div class="form-group mb-3">
                <label for="filter_dept_id">Department</label>
                <select class="form-select" id="filter_dept_id" name="filter_dept_id">
                  <option value="">--All--</option>
                  @foreach($departments as $dept)
                  <option value="{{ $dept->id }}" {{ old('filter_dept_id', $filterDeptId ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12 col-md-2">
              <button type="submit" class="btn btn-primary" style="margin-top: -50px;">Search</button>
            </div>
          </div>
          @if(isset($filterError) && $filterError)
          <div class="text-danger mb-2">{{ $filterError }}</div>
          @endif
        </form>
      </div>
    </div>

    @if(isset($ipdRecords) && $ipdRecords->isNotEmpty())
    <div class="card mt-3">
      <div class="card-body">
        <form id="diseasesForm" method="post" action="{{ url('/admin/diet-plan') }}">
          @csrf
          <input type="hidden" name="ipd_diet_plan_submit" value="1">
          <input type="hidden" name="filter_date" value="{{ $filterDate }}">
          <input type="hidden" name="filter_dept_id" value="{{ $filterDeptId ?? '' }}">
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="thead-dark">
                <tr>
                  <th style="width: 60%;">IPD Details</th>
                  <th style="width: 10%;">Morning</th>
                  <th style="width: 10%;">Afternoon</th>
                  <th style="width: 10%;">Evening</th>
                </tr>
              </thead>
              <tbody>
                @foreach($ipdRecords as $ipd)
                <tr>
                  <td>
                    <div class="small">
                      <strong>OPD No:</strong> {{ $ipd->opd_number }} &nbsp;|&nbsp; <strong> IPD No:</strong> {{ $ipd->ipd_number ?? '—' }}<br>
                      <strong>Patient:</strong> {{ $ipd->patient_name ?? '—' }}
                      @if($ipd->patient_age !== null || $ipd->gender)
                        ({{ $ipd->patient_age ?? '—' }}{{ $ipd->patient_age_unit ?? '' }} / {{ $ipd->gender ?? '—' }})
                      @endif
                      <br>
                      <strong>IPD Date:</strong> {{ $ipd->date ? $ipd->date->format('d/m/Y') : '—' }}
                    </div>
                  </td>
                  @php
                $ipdDate = $ipd->date ? $ipd->date->format('Y-m-d') : null;
                $dischargeDate = $ipd->discharge_date ? \Carbon\Carbon::parse($ipd->discharge_date)->format('Y-m-d') : null;
            @endphp
            <td>
                <select class="form-select form-select-sm select2"
                        name="morning[{{ $ipd->id }}]"
                        {{ $ipdDate == $filterDateDb ? 'disabled' : '' }}>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </td>

            <td>
                <select class="form-select form-select-sm select2"
                        name="afternoon[{{ $ipd->id }}]"
                        {{ $dischargeDate == $filterDateDb ? 'disabled' : '' }}>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </td>

            <td>
                <select class="form-select form-select-sm select2"
                        name="evening[{{ $ipd->id }}]"
                        {{ $dischargeDate == $filterDateDb ? 'disabled' : '' }}>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
          </div>
        </form>
      </div>
    </div>
    @elseif(isset($ipdRecords) && $ipdRecords->isEmpty() && isset($filterDate))
    <div class="card mt-3">
      <div class="card-body">
        <p class="text-muted mb-0">No ipd records found for the selected date and department.</p>
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
  $('#filterForm').on('submit', function(){
    if ($.trim($('#filter_date').val()) === '') {
      return false;
    }
  });
  $('#diseasesForm').on('submit', function(e){
    e.preventDefault();
    var $btn = $('#submitBtn');
    if ($btn.prop('disabled')) return false;
    $btn.prop('disabled', true).html('Please wait...');
    $.ajax({
      type: 'POST',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: $(this).attr('action'),
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res){
        $btn.prop('disabled', false).html('Submit');
        if (res.heading === 'Success') {
          swal("", res.msg, "success").then(function(){ document.getElementById('filterForm').submit(); });
        } else {
          swal("Error!", res.msg || 'Something went wrong.', "error");
        }
      },
      error: function(){
        $btn.prop('disabled', false).html('Submit');
        swal("Error!", 'Something went wrong. Please try again.', "error");
      }
    });
    return false;
  });
});
</script>
@endsection
