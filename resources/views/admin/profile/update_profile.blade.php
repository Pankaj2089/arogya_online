@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Manage Profile</h3>
                    <p class="text-subtitle text-muted">Update your account details.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Update Profile</h4>
                </div>
                <div class="card-body">
                <form class="form w-100" id="updateForm" action="#">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="old_banner" value="{!! $userData->photo !!}" />
                            <div class="form-group">
                                <label for="basicInput">Full Name</label>
                                <input type="text" class="form-control" placeholder="Enter Full Name" value="{!! $userData->name !!}" name="name" id="name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disabledInput">Email Address</label>
                                <input type="text" class="form-control" readonly value="{!! $userData->email !!}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disabledInput">Upload Profile Image</label>
                                <input class="form-control" type="file" name="banner" id="banner" autocomplete="off" accept="image/png, image/jpg" />
                            </div>
                        </div>
                        @if(!empty($userData->photo))
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="disabledInput">Profile Picture</label>
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <div class="cropped" id="cropped">
                                        <div class="cropped" id="cropped"><img src="{{URL::asset('public/admin/images/profile/')}}/{!! $userData->photo !!}" width="150"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="text-left">
                            <!--begin::Submit button-->
                            <button type="button" id="profile_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2">
                                <span class="indicator-label" id="formSubmit">Submit</span>
                                <span class="indicator-progress d-none">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <!--end::Submit button-->
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
 <!-- plugin js -->
 @stack('plugin-scripts')
<!-- end plugin js -->
<script>
    let saveDataURL = "{{url('/admin/save-profile')}}";
</script>
<script src="{{ asset('public/admin/js/pages/update-profile.js') }}"></script>
@stack('custom-scripts')

@endsection


