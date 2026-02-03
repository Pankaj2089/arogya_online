$(document).ready(function(){
    var formSubmitted = false;

    $('#form_submit').click(function(e){
        var flag = 0;
        if(!formSubmitted){
            if($.trim($("#department_id").val()) == ''){
                flag = 1;
                swal("Error!", 'Please select department.', "error");
                return false;
            }
            if($.trim($("#bed_no").val()) == ''){
                flag = 1;
                swal("Error!", 'Please enter bed number.', "error");
                return false;
            }
            if($.trim($("#bed_status").val()) == ''){
                flag = 1;
                swal("Error!", 'Please select bed status.', "error");
                return false;
            }
            if(flag == 0){
                $('#form_submit .indicator-label').addClass('d-none');
                $('#form_submit .indicator-progress').removeClass('d-none');
                var form = $('#pageForm')[0];
                var formData = new FormData(form);
                if($('#status').is(':checked')){
                    formData.append('status', '1');
                }
                formSubmitted = true;
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: saveDataURL,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(msg){
                        var obj = (typeof msg === 'string') ? JSON.parse(msg) : msg;
                        formSubmitted = false;
                        $('#form_submit .indicator-label').removeClass('d-none');
                        $('#form_submit .indicator-progress').addClass('d-none');
                        if(obj.heading == "Success"){
                            swal("", obj.msg, "success").then(function(){
                                window.location.assign(returnURL);
                            });
                        } else {
                            swal("Error!", obj.msg || 'Something went wrong.', "error");
                        }
                    },
                    error: function(){
                        formSubmitted = false;
                        $('#form_submit .indicator-label').removeClass('d-none');
                        $('#form_submit .indicator-progress').addClass('d-none');
                        swal("Error!", 'Something went wrong, please try again later.', "error");
                    }
                });
            }
        }
    });
});
