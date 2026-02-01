@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Edit Financial Year</h3>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{url('/admin/financial-years')}}">Financial Years</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Financial Year</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <section class="section">
    <form class="form w-100" id="pageForm" action="#">
      <div class="row">
        <div class="col-9 col-md-9">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="name">Year</label>
                    <select class="form-select" name="name" id="name" required>
                      <option value="">Select Year</option>
                      @php
                        $currentYear = (int) date('Y');
                        for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
                          $selected = (isset($rowData->name) && $rowData->name == (string)$y) ? 'selected' : '';
                          echo '<option value="' . $y . '" ' . $selected . '>' . $y . '</option>';
                        }
                      @endphp
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="opd_number">OPD Number</label>
                    <input type="number" class="form-control numberonly" min="0" step="1" placeholder="Enter OPD Number" value="{{ $rowData->opd_number ?? 0 }}" name="opd_number" id="opd_number">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="basicInput">Status</label>
                    <div class="form-check form-switch mt-2">
                      <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ (isset($rowData->status) && $rowData->status == 1) ? 'checked' : '' }}>
                      <label class="form-check-label" for="status">Active</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-3 col-md-3 ">
          <div class="card">
            <div class="col-md-12">
              <div class="text-left  p-3 p-l-20">
                <button type="button" id="form_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2"> <span class="indicator-label" id="formSubmit">Submit</span> <span class="indicator-progress d-none">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </section>
</div>
<script>
    $(document).ready(function () {
		$('.numberonly').keypress(function(e){
			var charCode = (e.which) ? e.which : event.keyCode;
			if(String.fromCharCode(charCode).match(/[^0-9]/g))
			return false;
		});
    });
    let saveDataURL = "{{url('/admin/edit-financial-year/'.$row_id)}}";
    let returnURL = "{{url('/admin/financial-years')}}";
</script>
<script src="{{ asset('public/admin/js/pages/financial-years/edit-page.js') }}"></script>
@endsection
