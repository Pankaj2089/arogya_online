@extends('layout.admin.dashboard')

@section('content')
<div class="page-heading">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Edit Customer</h3>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{url('/admin/users')}}">Customers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Customer</li>
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
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation"> <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home"
                                                    role="tab" aria-controls="home" aria-selected="true">General Info</a> </li>
                <li class="nav-item" role="presentation"> <a class="nav-link" id="description-tab" data-bs-toggle="tab" href="#address"
                                                    role="tab" aria-controls="address" aria-selected="false">Address Info</a> </li>
              </ul>
              <hr />
              <div class="tab-content mt-5" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="basicInput">Name</label>
                        <input type="text" class="form-control" placeholder="Enter Name" value="{{$rowData->name}}" name="name" id="name">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="basicInput">Email Address</label>
                        <input type="text" class="form-control" placeholder="Enter Email" value="{{$rowData->email}}" name="email" id="email">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="basicInput">Mobile</label>
                        <input type="text" class="numberonly form-control" maxlength="10" placeholder="Enter Mobile" value="{{$rowData->mobile}}" name="mobile" id="mobile">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="basicInput">Gender</label>
                        <select class="form-select" value="" name="gender" id="gender">
                          <option @if($rowData->gender == 'Male') selected='selected' @endif value="Male">Male</option>
                          <option @if($rowData->gender == 'Female') selected='selected' @endif value="Female">Female</option>
                          <option @if($rowData->gender == 'Other') selected='selected' @endif value="Other">Other</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="basicInput">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" id="profile_image" accept="image/*">
                        <input type="hidden" name="old_profile_image" value="{!! $rowData->photo !!}" />
                      </div>
                    </div>
                    @if($rowData->photo != "")
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="basicInput">&nbsp;</label>
                        <img src="{{URL::asset('public/img/users/')}}/{!! $rowData->photo !!}"  style="max-width: 80px;height: auto;"> </div>
                    </div>
                    @endif
                    </div>
                     
                </div>
                 
                <div class="tab-pane fade " id="address" role="tabpanel" aria-labelledby="address-tab">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="basicInput">Address</label>
                        <input type="text" class="form-control" placeholder="Enter Address" value="{{$rowData->address}}" name="address" id="address">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="basicInput">City</label>
                        <input type="text" class="form-control" placeholder="Enter City" value="{{$rowData->city}}" name="city" id="city">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="basicInput">Zipcode</label>
                        <input type="text" class="numberonly form-control" maxlength="6" placeholder="Enter Zipcode" value="{{$rowData->zipcode}}" name="zipcode" id="zipcode">
                      </div>
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
                <!--begin::Submit button-->
                <button type="button" id="form_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2"> <span class="indicator-label" id="formSubmit">Submit</span> <span class="indicator-progress d-none">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
                <!--end::Submit button--> 
              </div>
            </div>
          </div>
        </div>
      </div> 
    </form>
  </section>
</div>
<!-- end plugin js --> 
<script>
    $(document).ready(function () {
		$('.numberonly').keypress(function(e){
			var charCode = (e.which) ? e.which : event.keyCode
			if(String.fromCharCode(charCode).match(/[^0-9+]/g))
			return false;
		});
    });
    let saveDataURL = "{{url('/admin/edit-user/'.$row_id)}}";
    let returnURL = "{{url('/admin/users')}}";
</script> 
<script src="{{ asset('public/admin/js/pages/users/edit-page.js') }}"></script> 
@endsection 