$(document).ready(function(){
    var formSubmitted = false;
	$('#form_update_order').click(function(e){	
		var flag = 0;
        var submitBTN = $(this).attr('id');
        if(!formSubmitted){
            formSubmitted = false;
            if(flag == 0){
                $('#'+submitBTN+' .indicator-label').addClass('d-none');
                $('#'+submitBTN+' .indicator-progress').removeClass('d-none');

                var form = $('#orderForm')[0];
                var formData = new FormData(form);
                formSubmitted = true;
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: updateOrderURL,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(msg){
                        var obj = JSON.parse(msg);
                        window.onbeforeunload = null;
                        formSubmitted = false;
                        $('#'+submitBTN+' .indicator-label').removeClass('d-none');
                        $('#'+submitBTN+' .indicator-progress').addClass('d-none');
						
                        if(obj['heading'] == "Success"){
                            swal("", obj['msg'], "success").then((value) => {

                                if(submitBTN == "form_submit_order1"){
                                    window.location.assign(returnURL+'/'+obj['order_id']);
                                }else{
                                     window.location.assign(returnURL+'/'+obj['order_id']);
                                }
                            });
                        }else{
                            swal("Error!", obj['msg'], "error");
                            return false;
                        }
                    },error: function(ts) {
						console.log(ts);
                        formSubmitted = false;
                        $('#form_submit_order .indicator-label').removeClass('d-none');
                        $('#form_submit_order .indicator-progress').addClass('d-none');
                        swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
                        return false;
                    }
                });
            }
        }
	});
	
});