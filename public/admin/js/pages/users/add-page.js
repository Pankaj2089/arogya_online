    $(document).ready(function(){

    var formSubmitted = false;

	$('#form_submit').click(function(e){

		var flag = 0;

        if(!formSubmitted){

            formSubmitted = false;

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

                swal("Error!", 'Please Enter User Contact.', "error");

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

                        swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");

                        return false;

                    }

                });

            }

        }

	});

});

