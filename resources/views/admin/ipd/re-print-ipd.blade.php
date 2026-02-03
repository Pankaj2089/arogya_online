@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading no-print">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Re-print IPD</h3>
        <p class="text-subtitle text-muted">Enter IPD No. to view details and print.</p>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Re-print IPD</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <section class="section">
    <div class="card no-print">
      <div class="card-body">
        <form id="ipdLookupForm" method="post" action="{{ url('/admin/re-print-ipd') }}">
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
    @php
      $r = $ipdRecord;
      $u = $r->patient_age_unit ?? 'Years';
      $abbr = ($u === 'Years') ? 'Y' : ($u === 'Months' ? 'M' : 'D');
      $ageGender = ($r->patient_age ?? 0) . $abbr . ' / ' . ($r->gender ?? '—');
      $dept = $r->opdRegistration && $r->opdRegistration->department ? $r->opdRegistration->department : null;
      if (!$dept && $r->dept_id) {
        $dept = \App\Models\Department::find($r->dept_id);
      }
      $deptName = $dept ? $dept->name : 'N/A';
      $bedNo = $r->bedDistribution ? $r->bedDistribution->bed_no : 'N/A';
      $admitByName = $r->admitByUser ? $r->admitByUser->name : 'N/A';
    @endphp
    <div class="card mt-3 no-print">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title mb-0">IPD Details - {{ $r->ipd_number ?? $r->id }}</h5>
          <button type="button" class="btn btn-primary" id="btnPrint"><i class="bi bi-printer me-1"></i> Print</button>
        </div>
        <a href="{{ url('/admin/re-print-ipd') }}" class="btn btn-outline-secondary btn-sm mb-3">New search</a>
      </div>
    </div>

    <div class="card mt-2" id="ipd-print-area">
      <div class="card-body">
        <h5 class="card-title mb-3">IPD Registration - {{ $r->ipd_number ?? $r->id }}</h5>
        <div class="row">
          <div class="col-12 col-md-2">
            <div class="form-group mb-3">
              <label class="text-muted small">IPD No.</label>
              <div class="fw-semibold">{{ $r->ipd_number ?? $r->id }}</div>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group mb-3">
              <label class="text-muted small">OPD No.</label>
              <div>{{ $r->opd_number ?? '—' }}</div>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group mb-3">
              <label class="text-muted small">HID No.</label>
              <div>{{ $r->hid_number ?? '—' }}</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group mb-3">
              <label class="text-muted small">Fath./Husb Name</label>
              <div>{{ $r->fath_husb_name ?? '—' }}</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group mb-3">
              <label class="text-muted small">Patient Name</label>
              <div>{{ $r->patient_name ?? '—' }}</div>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group mb-3">
              <label class="text-muted small">Age/Gender</label>
              <div>{{ $ageGender }}</div>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group mb-3">
              <label class="text-muted small">Date</label>
              <div>{{ $r->date ? $r->date->format('d/m/Y') : '—' }}</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group mb-3">
              <label class="text-muted small">Address</label>
              <div>{{ $r->address ?? '—' }}</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group mb-3">
              <label class="text-muted small">Diagnosis</label>
              <div>{{ $r->diagnosis ?? '—' }}</div>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group mb-3">
              <label class="text-muted small">Category</label>
              <div>{{ $r->category ?? 'GENERAL' }}</div>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group mb-3">
              <label class="text-muted small">Bed No.</label>
              <div>{{ $bedNo }}</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group mb-3">
              <label class="text-muted small">Admit by</label>
              <div>{{ $admitByName }}</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group mb-3">
              <label class="text-muted small">Department</label>
              <div>{{ $deptName }}</div>
            </div>
          </div>
        </div>
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
  $('#btnPrint').on('click', function(){
    var printContent = document.getElementById('ipd-print-area').innerHTML;
    var printWindow = window.open('', '_blank');
    if (!printWindow) {
      swal("Error!", "Please allow popups to print.", "error");
      return;
    }
    printWindow.document.write(
      '<!DOCTYPE html><html><head><title>IPD Re-print</title>' +
      '<style>' +
      'body{ font-family: Arial, sans-serif; padding: 20px; font-size: 14px; }' +
      'h5{ margin-bottom: 15px; font-size: 18px; }' +
      '.row{ display: flex; flex-wrap: wrap; margin: 0 -8px; }' +
      '.row > [class*="col-"]{ padding: 0 8px; margin-bottom: 12px; box-sizing: border-box; }' +
      '.col-12.col-md-2{ width: 16.666%; } .col-12.col-md-4{ width: 33.333%; }' +
      '.form-group{ margin-bottom: 12px; }' +
      '.form-group label{ display: block; font-size: 11px; color: #6c757d; margin-bottom: 2px; }' +
      '.fw-semibold{ font-weight: 600; }' +
      '.small{ font-size: 0.875em; }' +
      '.text-muted{ color: #6c757d; }' +
      '.mb-3{ margin-bottom: 1rem; }' +
      '</style></head><body>' + printContent + '</body></html>'
    );
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function(){
      printWindow.print();
      printWindow.close();
    }, 250);
  });
});
</script>
@endsection
