@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Add New Admin</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{url('/admin/admins')}}">Admins</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Admin</li>
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
                                        <label for="basicInput">Type <span class="text-danger">*</span></label>
                                        <select class="form-select" name="type" id="type" required>
                                            <option value="">--Select--</option>
                                            <option value="Operator">Operator</option>
                                            <option value="Doctor">Doctor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" id="dept_id_wrapper" style="display: none;">
                                    <div class="form-group">
                                        <label for="dept_id">Department <span class="text-danger">*</span></label>
                                        <select class="form-select" name="dept_id" id="dept_id">
                                            <option value="">--Select--</option>
                                            @if(isset($departments) && $departments->count())
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="basicInput">Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Name" value="" name="name" id="name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="basicInput">Email Address</label>
                                        <input type="text" class="form-control" placeholder="Enter Email" value="" name="email" id="email">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="basicInput">Mobile</label>
                                        <input type="text" class="form-control numberonly" maxlength="10" placeholder="Enter Mobile" value="" name="mobile" id="mobile">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="basicInput">Password</label>
                                        <input type="password" class="form-control" maxlength="10" placeholder="Enter Password" value="" name="password" id="password">
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
		// Show/hide Department when Type changes (Doctor = show)
		function toggleDeptWrapper() {
			var typeVal = $('#type').val();
			if (typeVal === 'Doctor') {
				$('#dept_id_wrapper').css('display', 'block');
				$('#dept_id').prop('required', true);
			} else {
				$('#dept_id_wrapper').css('display', 'none');
				$('#dept_id').prop('required', false).val('');
			}
		}
		$('#type').on('change', toggleDeptWrapper);
		toggleDeptWrapper();
    }); 
    let saveDataURL = "{{url('/admin/add-admin')}}";
    let returnURL = "{{url('/admin/admins')}}";
</script>
<script src="{{ asset('public/admin/js/pages/admins/add-page.js') }}"></script>

@endsection