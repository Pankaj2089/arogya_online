@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Re-Schedule OPD</h3>
        <p class="text-subtitle text-muted">Re-schedule existing OPD registration.</p>
      </div>
      
    </div>
  </div>
  <section class="section">
    {{-- Step 1: Enter OPD No. --}}
    <div class="card">
      <div class="card-body">
        <form id="opdLookupForm" method="post" action="{{ url('/admin/re-schedule-opd') }}">
          @csrf
          <div class="row align-items-end">
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="opd_no">Enter OPD No.</label>
                <input type="text" class="form-control" id="opd_no" name="opd_no" placeholder="Enter OPD Number" value="{{ request()->old('opd_no') }}">
              </div>
            </div>
            <div class="col-12 col-md-2">
              <button type="submit" class="btn btn-primary" style="margin-top: -50px;">Submit</button>
            </div>
          </div>
          @if(isset($opdLookupError) && $opdLookupError)
          <div class="text-danger mb-2">{{ $opdLookupError }}</div>
          @endif
        </form>
      </div>
    </div>

    @if(isset($opdRecord) && $opdRecord)
    {{-- Step 2: Form (same layout as new-opd-registration, most fields disabled; only Department and Age editable) --}}
    <div class="card mt-3">
      <div class="card-body">
        <form class="form w-100" id="reScheduleForm" action="{{ url('/admin/re-schedule-opd') }}" method="post">
          @csrf
          @if($activeFinancialYear)
          <input type="hidden" name="financial_year_id" value="{{ $activeFinancialYear->id }}">
          @endif
          <input type="hidden" name="hid_number_to_use" value="{{ $opdRecord->hid_number }}">
          <input type="hidden" name="patient_name" value="{{ $opdRecord->patient_name }}">
          <input type="hidden" name="fath_husb_name" value="{{ $opdRecord->fath_husb_name ?? '' }}">
          <input type="hidden" name="address" value="{{ $opdRecord->address ?? '' }}">
          <input type="hidden" name="gender" value="{{ $opdRecord->gender ?? '' }}">
          <div class="row">
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="financial_year">Financial Year</label>
                <input type="text" class="form-control" value="{{ $activeFinancialYear ? $activeFinancialYear->name : '' }}" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="gender">Gender</label>
                <input type="text" class="form-control" value="{{ $opdRecord->gender ?? '—' }}" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="patient_name">Patient Name</label>
                <input type="text" class="form-control" value="{{ $opdRecord->patient_name }}" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="fath_husb_name">Fath/Husb. Name</label>
                <input type="text" class="form-control" value="{{ $opdRecord->fath_husb_name ?? '—' }}" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label>Patient Age</label>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <input type="number" class="form-control" name="patient_age" id="patient_age" value="{{ $opdRecord->patient_age }}" min="0" step="1" style="max-width: 100px;">
                  <select class="form-select" name="patient_age_unit" id="patient_age_unit" style="max-width: 120px;">
                    <option value="Years" {{ ($opdRecord->patient_age_unit ?? 'Years') == 'Years' ? 'selected' : '' }}>Years</option>
                    <option value="Months" {{ ($opdRecord->patient_age_unit ?? '') == 'Months' ? 'selected' : '' }}>Months</option>
                    <option value="Days" {{ ($opdRecord->patient_age_unit ?? '') == 'Days' ? 'selected' : '' }}>Days</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="address">Address</label>
                <input type="text" class="form-control" value="{{ $opdRecord->address ?? '—' }}" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="dept_id">Department</label>
                <select class="form-select" name="dept_id" id="dept_id">
                  <option value="">--Select--</option>
                  @foreach($departments as $dept)
                  <option value="{{ $dept->id }}" {{ (isset($opdRecord->dept_id) && $opdRecord->dept_id == $dept->id) ? 'selected' : '' }}>{{ $dept->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="date">Date</label>
                <input type="text" class="form-control" value="{{ date('m/d/Y') }}" disabled readonly>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <button type="submit" class="btn btn-primary" id="reScheduleSubmitBtn">Submit</button>
              <a href="{{ url('/admin/re-schedule-opd') }}" class="btn btn-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
    @endif
  </section>
</div>
<script>
$(document).ready(function(){
  $('#opdLookupForm').on('submit', function(e){
    if ($.trim($('#opd_no').val()) === '') {
      e.preventDefault();
      swal("Error!", "Please enter OPD number.", "error");
      return false;
    }
  });
  $('#patient_age').on('keypress', function(e){
    var charCode = (e.which) ? e.which : event.keyCode;
    if (String.fromCharCode(charCode).match(/[^0-9]/g)) return false;
  });
  $('#reScheduleForm').on('submit', function(e){
    e.preventDefault();
    var $btn = $('#reScheduleSubmitBtn');
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
          swal("", res.msg, "success").then(function(){ window.location.href = '{{ url("/admin/new-opd-registration") }}'; });
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
