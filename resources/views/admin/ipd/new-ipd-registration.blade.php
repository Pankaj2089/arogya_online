@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>IPD Patient Registration</h3>
        <p class="text-subtitle text-muted">In-patient registration. Enter OPD No. to load patient details.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">New IPD Registration</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <section class="section">
    {{-- Step 1: Enter OPD No. (same as re-schedule-opd) --}}
    <div class="card">
      <div class="card-body">
        <form id="opdLookupForm" method="post" action="{{ url('/admin/new-ipd-registration') }}">
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
    {{-- Step 2: IPD Registration form (same structure as screenshot, current design; no Ward, Ward-Type, Room No.) --}}
    @php
      $dept = $opdRecord->department;
      $deptName = $dept ? $dept->name : 'N/A';
      $u = $opdRecord->patient_age_unit ?? 'Years';
      $abbr = ($u === 'Years') ? 'Y' : ($u === 'Months' ? 'M' : 'D');
      $ageGender = ($opdRecord->patient_age ?? 0) . $abbr . ' / ' . ($opdRecord->gender ?? '—');
    @endphp
    <div class="card mt-3">
      <div class="card-body">
        <form class="form w-100" id="ipdRegistrationForm" action="{{ url('/admin/new-ipd-registration') }}" method="post">
          @csrf
          <input type="hidden" name="opd_registration_id" value="{{ $opdRecord->id }}">
          <input type="hidden" name="category" value="GENERAL">
          <div class="row">
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label>OPD No.</label>
                <input type="text" class="form-control" value="{{ $opdRecord->opd_number }}" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" placeholder="50" min="0" step="0.01" value="{{ request()->old('amount') }}">
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="fath_husb_name">Fath./Husb Name</label>
                <input type="text" class="form-control" id="fath_husb_name" name="fath_husb_name" placeholder="Father/Husband Name" value="{{ old('fath_husb_name', $opdRecord->fath_husb_name ?? '') }}">
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label>Patient Name</label>
                <input type="text" class="form-control" value="{{ $opdRecord->patient_name }}" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label>Age/Gender</label>
                <input type="text" class="form-control" value="{{ $ageGender }}" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="date">Date</label>
                <input type="text" class="form-control datepicker" id="date" name="date" placeholder="DD/MM/YYYY" value="{{ old('date', date('m/d/Y')) }}" autocomplete="off">
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Please Fill Address" value="{{ old('address', $opdRecord->address ?? '') }}">
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="diagnosis">Diagnosis</label>
               @if(!empty($disease?->name))
                  <input type="text"
                        class="form-control"
                        value="{{ $disease->name }}"
                        disabled>

                  <input type="hidden"
                        name="diagnosis"
                        value="{{ $disease->name }}">
              @else
                  <input type="text"
                        class="form-control"
                        id="diagnosis"
                        name="diagnosis"
                        placeholder="Diagnosis"
                        value="{{ old('diagnosis') }}">
              @endif
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label>Category</label>
                <input type="text" class="form-control" value="GENERAL" disabled readonly>
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group mb-3">
                <label for="bed_distribution_id">Bed No. <small class="text-muted">(available only)</small></label>
                <select class="form-select" id="bed_distribution_id" name="bed_distribution_id">
                  <option value="">--Select--</option>
                  @foreach($beds as $bed)
                  <option value="{{ $bed->id }}" {{ old('bed_distribution_id') == $bed->id ? 'selected' : '' }}>{{ $bed->bed_no }}</option>
                  @endforeach
                </select>
                @if(isset($beds) && $beds->isEmpty())
                <small class="text-danger">No available beds.</small>
                @endif
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label for="admit_by_user_id">Admit by</label>
                <select class="form-select" id="admit_by_user_id" name="admit_by_user_id">
                  <option value="">--Select--</option>
                  @foreach($doctors as $doc)
                  <option value="{{ $doc->id }}" {{ old('admit_by_user_id') == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="form-group mb-3">
                <label>Department</label>
                <input type="text" class="form-control" value="{{ $deptName }} Unit: N/A" disabled readonly>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <button type="submit" class="btn btn-primary" id="ipdSubmitBtn">Submit</button>
              <a href="{{ url('/admin/new-ipd-registration') }}" class="btn btn-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
    @endif

    @if(isset($lastIpd) && $lastIpd)
    {{-- Last IPD strip: show whenever a record exists (first screen or form screen) --}}
    <div class="card mt-3 border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="opd-last-strip d-flex flex-wrap align-items-center justify-content-between gap-3 px-4 py-3">
          <span class="opd-strip-item fw-bold"><span class="opd-strip-label">Last IPD No: - </span>{{ $lastIpd->ipd_number ?? $lastIpd->id }}</span>
          <span class="opd-strip-item fw-bold"><span class="opd-strip-label">Patient Name: - </span>{{ $lastIpd->patient_name ?? $lastIpd->opdRegistration->patient_name ?? 'N/A' }}</span>
          <span class="opd-strip-item fw-bold"><span class="opd-strip-label">Category: - </span>{{ $lastIpd->category ?? 'GENERAL' }}</span>
          <span class="opd-strip-item fw-bold"><span class="opd-strip-label">Date/Time: - </span>{{ $lastIpd->date ? $lastIpd->date->format('l, F j, Y') : 'N/A' }}</span>
        </div>
      </div>
    </div>
    @endif
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
  $('#opdLookupForm').on('submit', function(e){
    if ($.trim($('#opd_no').val()) === '') {
      e.preventDefault();
      swal("Error!", "Please enter OPD number.", "error");
      return false;
    }
  });
  $('#date').datepicker({ dateFormat: 'mm/dd/yy' });
  $('#ipdRegistrationForm').on('submit', function(e){
    e.preventDefault();
    var $btn = $('#ipdSubmitBtn');
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
          swal("", res.msg, "success").then(function(){ window.location.href = '{{ url("/admin/new-ipd-registration") }}'; });
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
