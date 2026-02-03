@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>New OPD Registration</h3>
        <p class="text-subtitle text-muted">Patient registration form.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">New OPD Registration</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <section class="section">
    <div class="card">
      <div class="card-body">
        <form class="form w-100" id="opdRegistrationForm" action="{{ url('/admin/new-opd-registration') }}" method="post">
          @csrf
          @if($activeFinancialYear)
          <input type="hidden" name="financial_year_id" id="financial_year_id" value="{{ $activeFinancialYear->id }}">
          @endif
          <div class="row">
            {{-- Left Column --}}
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="financial_year">Financial Year</label>
                <input type="text" class="form-control" id="financial_year" name="financial_year" value="{{ $activeFinancialYear ? $activeFinancialYear->name : '' }}" disabled readonly>
              </div>
              </div>
              <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="gender">Gender</label>
                <select class="form-select" id="gender" name="gender">
                  <option value="">--Select--</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              </div>
              <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="patient_name">Patient Name</label>
                <input type="text" class="form-control" id="patient_name" name="patient_name" placeholder="First and last name">
              </div>
              </div>
              <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="fath_husb_name">Fath/Husb. Name <span class="text-muted">(optional)</span></label>
                <input type="text" class="form-control" id="fath_husb_name" name="fath_husb_name" placeholder="First and last name">
              </div>
              </div>
              <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label>Patient Age</label>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <input type="number" class="form-control" id="patient_age" name="patient_age" placeholder="Age" min="0" step="1" style="max-width: 100px;">
                  <select class="form-select" id="patient_age_unit" name="patient_age_unit" style="max-width: 120px;">
                    <option value="Years">Years</option>
                    <option value="Months">Months</option>
                    <option value="Days">Days</option>
                  </select>
                </div>
              </div>
              </div>
              <div class="col-12 col-md-6">
              <div class="form-group mb-3">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Address">
              </div>
              </div>
              
            {{-- Right Column --}}
            
             
              <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="dept_id">Department</label>
                <select class="form-select" id="dept_id" name="dept_id">
                  <option value="">--Select--</option>
                  @foreach($departments as $dept)
                  <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="date">Date</label>
                <input type="text" class="form-control" id="date" name="date" value="{{ date('m/d/Y') }}" placeholder="Select date" readonly>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <button type="submit" class="btn btn-primary">Submit</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    {{-- Last OPD strip (after form) --}}
    <div class="card mt-3 border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="opd-last-strip d-flex flex-wrap align-items-center justify-content-between gap-3 px-4 py-3">
          <span class="opd-strip-item fw-bold"><span class="opd-strip-label">Last OPD No: - </span>{{ $lastOpd ? $lastOpd->opd_number : 'N/A' }}</span>
          <span class="opd-strip-item fw-bold"><span class="opd-strip-label">Patient Name: - </span>{{ $lastOpd ? $lastOpd->patient_name : 'N/A' }}</span>
          <span class="opd-strip-item fw-bold"><span class="opd-strip-label">Date/Time: - </span>{{ $lastOpd && $lastOpd->date ? date('l, F j, Y', strtotime($lastOpd->date)) : 'N/A' }}</span>
        </div>
      </div>
    </div>
  </section>
</div>
<style>
.opd-last-strip {
  background-color: var(--bs-primary);
  color: #fff;
  border: 2px dashed rgba(255,255,255,0.3);
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.opd-strip-label { opacity: 0.95; font-weight: 600; }
.opd-strip-item { white-space: nowrap; }
</style>
<script>
$(document).ready(function(){
  // Restrict patient age to integers only
  $('#patient_age').on('keypress', function(e){
    var charCode = (e.which) ? e.which : event.keyCode;
    if (String.fromCharCode(charCode).match(/[^0-9]/g)) return false;
  });
  // Form submit via AJAX
  $('#opdRegistrationForm').on('submit', function(e){
    e.preventDefault();
    var $btn = $(this).find('button[type="submit"]');
    if ($btn.prop('disabled')) return false;
    if ($.trim($('#patient_name').val()) === '') {
      swal("Error!", 'Please enter patient name.', "error");
      return false;
    }
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
          swal("", res.msg, "success").then(function(){ location.reload(); });
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
// Date picker - init after jquery-ui is loaded (layout loads it after content)
$(window).on('load', function(){
  if ($.fn.datepicker) {
    $('#date').datepicker({
      dateFormat: 'mm/dd/yy',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true
    });
  }
});
</script>
@endsection
