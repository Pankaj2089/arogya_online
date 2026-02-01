$(document).ready(function(){
    var formSubmitted = false;
	$('#profile_submit').click(function(e){
		var flag = 0;
        if(!formSubmitted){
            formSubmitted = false;
            if($.trim($("#admin_email").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Enter Your Email.', "error");
                return false;
            }
            if(flag == 0){
                $('#profile_submit .indicator-label').addClass('d-none');
                $('#profile_submit .indicator-progress').removeClass('d-none');
                var form = $('#updateForm')[0];
                var formData = new FormData(form);
                formSubmitted = true;
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: saveDataURL,
					dataType:'JSON',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(obj){
                       // var obj = JSON.parse(msg);
                        formSubmitted = false;
                        $('#profile_submit .indicator-label').removeClass('d-none');
                        $('#profile_submit .indicator-progress').addClass('d-none');
                        if(obj.heading == "Success"){
                            swal("", obj.msg, "success").then((value) => {
                                location.reload();
                            });
                        }else{
                            swal("Error!", obj.msg, "error");
                            return false;
                        }
                    },error: function(ts) {
                        formSubmitted = false;
                        $('#form_submit .indicator-label').removeClass('d-none');
                        $('#form_submit .indicator-progress').addClass('d-none');
                        swal("Error!", 'Something went wrong, please try after sometime.', "error");
                        return false;
                    }
                });
            }
        }
	});
});
