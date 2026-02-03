@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Add Bed Distribution</h3>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{url('/admin/bed-distributions')}}">Bed Distributions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Bed</li>
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
                    <label for="department_id">Department</label>
                    <select class="form-select" name="department_id" id="department_id" required>
                      <option value="">Select Department</option>
                      @foreach($departments as $dept)
                      <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="bed_no">Bed No.</label>
                    <input type="text" class="form-control" name="bed_no" id="bed_no" placeholder="Bed Number" maxlength="50" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="gender">Gender</label>
                    <select class="form-select" name="gender" id="gender">
                      <option value="">--Select--</option>
                      <option value="M">Male</option>
                      <option value="F">Female</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="bed_status">Bed Status</label>
                    <select class="form-select" name="bed_status" id="bed_status" required>
                      <option value="">Select Status</option>
                      <option value="available">Available</option>
                      <option value="occupied">Occupied</option>
                      <option value="reserved">Reserved</option>
                      <option value="maintenance">Maintenance</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="basicInput">Status</label>
                    <div class="form-check form-switch mt-2">
                      <input class="form-check-input" type="checkbox" name="status" id="status" value="1" checked>
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
              <div class="text-left p-3 p-l-20">
                <button type="button" id="form_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2"><span class="indicator-label" id="formSubmit">Submit</span> <span class="indicator-progress d-none">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </section>
</div>
<script>
    let saveDataURL = "{{url('/admin/add-bed-distribution')}}";
    let returnURL = "{{url('/admin/bed-distributions')}}";
</script>
<script src="{{ asset('public/admin/js/pages/bed-distributions/add-page.js') }}"></script>
@endsection
