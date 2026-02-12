@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Patient Discharge</h3>
        <p class="text-subtitle text-muted">Enter IPD No. to discharge patient and free the bed.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Patient Discharge</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <section class="section">
    <div class="card">
      <div class="card-body">
        <form id="ipdLookupForm" method="post" action="{{ url('/admin/patient-discharge') }}">
          @csrf
          <div class="row align-items-end">
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="ipd_no">Enter IPD No.</label>
                <input type="text" class="form-control" id="ipd_no" name="ipd_no" placeholder="Enter IPD Number" value="{{ request()->old('ipd_no', isset($ipdRecord) ? $ipdRecord->ipd_number : '') }}">
              </div>
            </div>
            <div class="col-12 col-md-2">
              <button type="submit" class="btn btn-primary" style="margin-top: -50px;">Submit</button>
            </div>
          </div>
          @if(isset($ipdLookupError) && $ipdLookupError)
          <div class="text-danger mb-2">{{ $ipdLookupError }}</div>
          @endif
        </form>
      </div>
    </div>

    @if(isset($ipdRecord) && $ipdRecord)
    <div class="card mt-3">
      <div class="card-body">
        <h5 class="card-title mb-3">Discharge – IPD No. {{ $ipdRecord->ipd_number ?? $ipdRecord->id }}</h5>
        <p class="text-muted small mb-3">Patient: {{ $ipdRecord->patient_name ?? 'N/A' }}</p>
        <form class="form w-100" id="dischargeForm" action="{{ url('/admin/patient-discharge') }}" method="post">
          @csrf
          <input type="hidden" name="ipd_registration_id" value="{{ $ipdRecord->id }}">
          <div class="row">
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="discharge_date">Discharge Date <span class="text-danger">*</span></label>
                <input type="text" class="form-control datepicker" id="discharge_date" name="discharge_date" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y') }}" autocomplete="off">
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="discharge_date">Discharge Type  <span class="text-danger">*</span></label>
                <select class="form-select" id="discharge_type" name="discharge_type" >
                  <option value="Normal">Normal</option>
                  <option value="Dama">Dama</option>
                  <option value="Absconded">Absconded</option>
                  <option value="Death">Death</option>
                </select>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="discharge_dept_id">Department</label>
                <select class="form-select"
                        id="discharge_dept_id"
                        {{$ipdRecord->opdRegistration->dept_id > 0 ? 'disabled':''}}>

                    <option value="">--Select--</option>
                    @foreach($departments as $dept)
                      <option value="{{ $dept->id }}"
                        {{$ipdRecord->opdRegistration->dept_id == $dept->id ? 'selected':''}}>
                        {{ $dept->name }}
                      </option>
                    @endforeach
                  </select>

                @if($ipdRecord->opdRegistration->dept_id > 0)
                  <input type="hidden"
                        name="discharge_dept_id"
                        value="{{ $ipdRecord->opdRegistration->dept_id }}">
                @endif
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <button type="submit" class="btn btn-primary" id="dischargeBtn">Discharge Patient</button>
              <a href="{{ url('/admin/patient-discharge') }}" class="btn btn-secondary">Cancel</a>
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
  $('#ipdLookupForm').on('submit', function(e){
    if ($.trim($('#ipd_no').val()) === '') {
      e.preventDefault();
      swal("Error!", "Please enter IPD number.", "error");
      return false;
    }
    
  });
  $('#discharge_date').datepicker({
    dateFormat: 'mm/dd/yy',
    maxDate: 0,
    minDate: '{{ isset($ipdRecord) && $ipdRecord->date ? $ipdRecord->date->format("m/d/Y") : "" }}'
  });
  $('#dischargeForm').on('submit', function(e){
    e.preventDefault();
    var $btn = $('#dischargeBtn');
    if ($btn.prop('disabled')) return false;
    $btn.prop('disabled', true).html('Please wait...');
    $.ajax({
      type: 'POST',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: $(this).attr('action'),
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res){
        $btn.prop('disabled', false).html('Discharge Patient');
        if (res.heading === 'Success') {
          swal("", res.msg, "success").then(function(){ window.location.href = '{{ url("/admin/patient-discharge") }}'; });
        } else {
          swal("Error!", res.msg || 'Something went wrong.', "error");
        }
      },
      error: function(){
        $btn.prop('disabled', false).html('Discharge Patient');
        swal("Error!", 'Something went wrong. Please try again.', "error");
      }
    });
    return false;
  });
});
</script>
@endsection
