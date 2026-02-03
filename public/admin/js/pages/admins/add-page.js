$(document).ready(function(){
    var formSubmitted = false;
    function toggleDeptWrapper(){
        if($("#type").val() === 'Doctor'){
            $('#dept_id_wrapper').show();
            $('#dept_id').prop('required', true);
        } else {
            $('#dept_id_wrapper').hide();
            $('#dept_id').prop('required', false).val('');
        }
    }
    $('#type').on('change', toggleDeptWrapper);
    toggleDeptWrapper();
	$('#form_submit').click(function(e){
		var flag = 0;
        if(!formSubmitted){
            formSubmitted = false;
			var validateMobNum= /^\d*(?:\.\d{1,2})?$/;
			var mobileNum = $.trim($("#mobile").val());
            if(!$("#type").val() || $.trim($("#type").val()) == ''){
                flag = 1;
                swal("Error!", 'Please select Type.', "error");
                return false;
            }
            if($("#type").val() === 'Doctor' && (!$("#dept_id").val() || $.trim($("#dept_id").val()) == '')){
                flag = 1;
                swal("Error!", 'Please select Department.', "error");
                return false;
            }
            if($.trim($("#name").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Enter Name.', "error");
                return false;
            }
            if($.trim($("#email").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Enter User Email Address.', "error");
                return false;
            }
			if($.trim($("#mobile").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Enter User Mobile Number.', "error");
                return false;
            }
            if(flag == 0){
                $('#form_submit .indicator-label').addClass('d-none');
                $('#form_submit .indicator-progress').removeClass('d-none');
                var form = $('#pageForm')[0];
                var formData = new FormData(form);
                formSubmitted = true;
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: saveDataURL,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(msg){
                        var obj = JSON.parse(msg);
                        formSubmitted = false;
                        $('#form_submit .indicator-label').removeClass('d-none');
                        $('#form_submit .indicator-progress').addClass('d-none');
                        if(obj['heading'] == "Success"){
                            swal("", obj['msg'], "success").then((value) => {
                                window.location.assign(returnURL);
                            });
                        }else{
                            swal("Error!", obj['msg'], "error");
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
