@extends('layout.admin.login')
@section('content')
<div id="auth">
    <div class="row h-100">

        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right"></div>
        </div>
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <!--<div class="auth-logo">
                    <a href="{{url('/admin')}}"><img src="{{ asset('public/admin/images/logo/logo.png') }}" style="height:auto" alt="Logo"></a>
                </div>-->
                <h1 class="auth-title">Log in.</h1>
                <p class="auth-subtitle mb-5">Log in with your data that you entered during registration.</p>

                <form action="#" method="post" id="adminLoginForm">
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" class="form-control form-control-xl" value="{{$cookieUsername}}" name="email"  id="email" placeholder="Username">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input value="{{$cookiePassword}}" type="password" name="password" id="password" class="form-control form-control-xl" placeholder="Password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                    <div class="form-check form-check-lg d-flex align-items-end">
                        <input class="form-check-input me-2" type="checkbox" id="flexCheckDefault" checked value="1">
                        <label class="form-check-label text-gray-600" for="flexCheckDefault">
                            Keep me logged in
                        </label>
                    </div>
                    <button type="button" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" id="loginSubmit">Log in</button>
                </form>
                <div class="text-center mt-5 text-lg fs-4 d-none">
                    <p><a class="font-bold" href="auth-forgot-password.html">Forgot password?</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
let adminLoginURL = "{{url('/admin/admin-login')}}";
let dashboardURL = "{{url('/admin/dashboard')}}";
$(document).ready(function(){
	$("#email, #password").on('keyup', function (e) {
		if (e.keyCode == 13) {
			$('#loginSubmit').trigger('click');
		}
	});


	$('#loginSubmit').click(function(e){
		var flag = 0;
		if($.trim($("#email").val()) == ''){
			flag = 1;
            showMessage('Please Enter Account Username.');
			return false;
		}
		if($.trim($("#password").val()) == ''){
			flag = 1;
            showMessage('Please Enter Account Password.');
			return false;
		}
		if(flag == 0){
            $('#loginSubmit').html('Processing...');
			$.ajax({
				type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url: adminLoginURL,
				data: $('#adminLoginForm').serialize(),
				beforeSend:function(){$('#loginSubmit').removeClass('login-btn').addClass('login-btn-processing').val('Processing...'); },
				success: function(msg){
                    var obj = JSON.parse(msg);
                    $('#loginSubmit').html('Log In');
					if(obj['heading'] == "Success"){
                        window.location.assign(dashboardURL);
					}else{
                        showMessage(obj['msg']);
						return false;
					}
				},error: function(ts) {
                    showMessage('Some thing want to wrong, please try after sometime.');
                    return false;
				}
			});
		}
	});
});
function showMessage(msg){
    swal("Error!", msg, "error");
}
</script>
@endsection
