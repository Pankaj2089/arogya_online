$(document).ready(function(){
    var formSubmitted = false;
    $('#profile_submit').click(function(e){
        var flag = 0;
        if(!formSubmitted){
            formSubmitted = false;
            if($.trim($("#current_password").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Enter Your Current Password.', "error");
                return false;
            }else if($.trim($("#password").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Enter Your New Password.', "error");
                return false;
            }else if($.trim($("#confirm_password").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Enter Confirm Password.', "error");
                return false;
            }else if($.trim($("#password").val()) != $.trim($("#confirm_password").val())){
                flag = 1;
                swal("Error!", 'The confirm password and password must match.', "error");
                return false;
            }
            if(flag == 0){
                $('#profile_submit .indicator-label').addClass('d-none');
                $('#profile_submit .indicator-progress').removeClass('d-none');
                formSubmitted = true;
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: saveDataURL,
                    data: $('#updateForm').serialize(),
                    success: function(msg){
                        var obj = JSON.parse(msg);
                        formSubmitted = false;
                        $('#profile_submit .indicator-label').removeClass('d-none');
                        $('#profile_submit .indicator-progress').addClass('d-none');
                        if(obj['heading'] == "Success"){
                            swal("", obj['msg'], "success").then((value) => {
                                location.reload();
                            });

                        }else{
                            swal("Error!", obj['msg'], "error");
                            return false;
                        }
                    },error: function(ts) {
                        formSubmitted = false;
                        $('#form_submit .indicator-label').removeClass('d-none');
                        $('#form_submit .indicator-progress').addClass('d-none');
                        swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
                        return false;
                    }
                });
            }
        }
    });
});
