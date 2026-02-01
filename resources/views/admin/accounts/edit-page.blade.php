@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Account</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{url('/admin/accounts')}}">Accounts</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Account</li>
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
                                    <input type="text" class="form-control numberonly" maxlength="10" placeholder="Enter Mobile" value="{{$rowData->mobile}}" name="mobile" id="mobile">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="basicInput">Password</label>
                                    <input type="password" class="form-control" placeholder="Enter Password" value="" name="password" id="password">
                                </div>
                            </div>
                            <div class="text-left  p-3 p-l-20">
                        <!--begin::Submit button-->
                        <button type="button" id="form_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2">
                            <span class="indicator-label" id="formSubmit">Submit</span>
                            <span class="indicator-progress d-none">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <!--end::Submit button-->
                    </div>
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
	let saveDataURL = "{{url('/admin/edit-account/'.$row_id)}}";
    let returnURL = "{{url('/admin/accounts')}}";
</script>
<script src="{{ asset('public/admin/js/pages/accounts/edit-page.js') }}"></script>

@endsection