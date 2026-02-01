function searchCustomerAddressView(customer_id){
	if(customer_id != ''){		
		$.ajax({
			type: 'POST',
			dataType: 'JSON',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: viewDataURL,
			data: {customer_id:customer_id},
			success: function(response){								
				$('#replace_customer').html(response);
			},error: function(ts) {
				console.log(ts);
				swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
				return false;
			}
		});	
	}
}

$(document).on('change','#customer_name',function(){
	var customer_id = $(this).val();
	
	$('.customer_info_value').val(null);
	$('#edit_user_div').hide();
	$('#replace_customer').html(null);
	$('#edit_user_div').show();
	if(customer_id != ''){
		$.ajax({
			type: 'POST',
			dataType: 'JSON',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: saveDataURL,
			data: {customer_id:customer_id},
			success: function(response){
				$('#edit_user_div').show();
				if(response.status == true){
					$('#country_ids').val(response.data.country);
					$('#state_ids').val(response.data.state);
					$('#city_ids').val(response.data.city);

					getStateByCountry(response.data.country);
					getCityByState(response.data.state);
					//console.log(response.data);
					$('#customer_id_value').val(response.data.id);
					$('#name').val(response.data.name);
					$('#email').val(response.data.email);
					$('#mobile').val(response.data.mobile);
					$('#gender').val(response.data.gender);
					$('#dob').val(response.data.dob);
					$('#address').val(response.data.address);
					$('#country').val(response.data.country);
					$('#state').val(response.data.state);
					$('#city').val(response.data.city);
					$('#zipcode').val(response.data.zipcode);
										
					if(response.address_status == true){
						$('#billing_address').val(response.data.billing_address);
						$('#billing_city').val(response.data.billing_city);
						$('#billing_country').val(response.data.billing_country);
						$('#billing_first_name').val(response.data.billing_first_name);
						$('#billing_last_name').val(response.data.billing_last_name);
						$('#billing_phone').val(response.data.billing_phone);
						$('#billing_state').val(response.data.billing_state);
						$('#billing_zipcode').val(response.data.billing_zipcode);
						
						$('#shipping_address').val(response.data.shipping_address);
						$('#shipping_city').val(response.data.shipping_city);		
						$('#shipping_country').val(response.data.shipping_country);		
						$('#shipping_first_name').val(response.data.shipping_first_name);		
						$('#shipping_last_name').val(response.data.shipping_last_name);		
						$('#shipping_phone').val(response.data.shipping_phone);		
						$('#shipping_state').val(response.data.shipping_state);		
						$('#shipping_zipcode').val(response.data.shipping_zipcode);		
					}
					
				}else{
					$('.customer_info_value').val(null);
				}
				searchCustomerAddressView(customer_id)
			},error: function(ts) {
				console.log(ts);
				swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
				return false;
			}
		});
	}else{
		$('#edit_user_div').show();
		$('#edit_user_btn').trigger('click');
	}	
});


var x = $('#myProductTbl >tbody >tr').length;
function add_more(){	
	var count = x++;
	$('.addMoreBtn').html('...');
	$.ajax({
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		url: loadProductURL,
		data: {count:count},
		success: function(msg){
			$('.add_more').append(msg);
			$('.addMoreBtn').html('Add More');
			return false;
		},error: function(ts){
			console.log(ts);
            $('#searchbuttons').html('Search');
            $('#error500').modal('show');
        }
	});
}

function remove(row){
	$('#remove_'+row).remove();
	setPrice(row);
}

function setPrice(counter){		
	var qty = $('#product_qty'+counter).val();
	var price = $('#product_price'+counter).val();
	
	if(qty == '' || qty == 'undefined'){
		$('#product_qty'+counter).val(1);
		qty = 1;	
	}
	if(price == '' || price == 'undefined'){
		$('#product_price'+counter).val(0);
		price = 0;
	}	
	
	var total_product = (parseInt(price)*parseInt(qty));
	var totalPrice = parseInt(total_product);
	
	$('#total'+counter).val(totalPrice.toFixed(2));				
	setTotal();		
}

$(document).on('change','#product_name',function(){	
	let value = $(this).val();
	let counter = $(this).attr('row_id');
	if(value != ''){
		$.ajax({
			type: 'POST',
			dataType: 'JSON',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: searchDataURL,
			data: {value:value},
			success: function(response){
				var pid = response.id;
				var qty = response.product_qty;
				var price = response.product_price;
				var product_code = response.product_code;
				//var total = parseInt(ui.item.price)+parseInt(labour);
				var sum = parseInt(price);
				var total = (parseFloat(sum));
				if(qty > 0 && qty <= 10){
					var $html = '';
					for(var i = 1; i <= qty; i++) {
					  $html+= '<option value="'+i+'">'+i+'</option>';
					}
					$('#product_qty'+counter).val($html);
					$('#product_qty'+counter).val(1);
				}
				$('#product_id'+counter).val(pid);				
				$('#product_code'+counter).val(product_code);
				$('#product_price'+counter).val(price);
				$('#total'+counter).val(total);
				
				//$(this).parent().next().next().find('.product_code').val(product_code);
				
				setTotal();	
			},error: function(ts) {
				console.log(ts);
				swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
				return false;
			}
		});
	}
});

function setTotal(){		
	var gstchr = '';
	var grandTotal = 0;
	$("input[name='total[]']").each(function(){
		var firstAmt = parseInt($(this).val());
		if($.isNumeric(firstAmt)){
			grandTotal = parseFloat(grandTotal)+parseFloat(firstAmt);
		}
	});
	$("#grandTotal").text(grandTotal.toFixed(2));
	$(".total_sum").val(grandTotal.toFixed(2));
	$('#hiddenTotal').val(grandTotal.toFixed(2));
}

$(document).on('click','#submit_customer',function(){
	var flag = 0;
	var submitBTN = $(this).attr('id');
	//if(!formSubmitted){
		formSubmitted = false;			
		
		if($.trim($("#customer_id_value").val()) == ''){
			//flag = 1;
			//swal("Error!", 'Please Enter Customer Information.', "error");
		   // return false;
		}
		if($.trim($("#name").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Customer name.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#email").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Customer Email.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#mobile").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Customer Contact.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		
		if($.trim($("#billing_address").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Billing Address.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#billing_country").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Billing Country.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#billing_state").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Billing State.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#billing_city").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Billing City.', "error");
			return false;
		}
		if($.trim($("#billing_zipcode").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Billing Zipcode.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		
		if($.trim($("#shipping_address").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Shiping Address.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#shipping_country").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Shipping Country.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#shipping_state").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Shipping State.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#shipping_city").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Shippping City.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}
		if($.trim($("#shipping_zipcode").val()) == ''){
			flag = 1;
			swal("Error!", 'Please Enter Shipping Zipcode.', "error");
			$('#edit_order_modal').modal('show');
			return false;
		}			

		if(flag == 0){
			var form = $('#orderForm')[0];
			var formData = new FormData(form);
			formSubmitted = true;
			$('#submit_customer').html('Processing...');
			$('#submit_customer').attr('disabled',true);
			$.ajax({
				type: 'POST',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url: saveCustomerURL,
				data: formData,
				processData: false,
				contentType: false,
				success: function(msg){
					var obj = JSON.parse(msg);
					window.onbeforeunload = null;
					formSubmitted = false;
					if(obj['heading'] == "Success"){	
						$('#edit_order_modal').modal('hide');						
						swal("", obj['msg'], "success").then((value) => {
							formSubmitted = true;
							let cust_id = obj['customer_id'];
							getCustomers(cust_id);							
							//$('#edit_user_btn').trigger('click');
						});
						$('#submit_customer').html('Submit');
						$('#submit_customer').attr('disabled',false);
					}else{
						swal("Error!", obj['msg'], "error");
						$('#submit_customer').html('Submit');
						$('#submit_customer').attr('disabled',false);
						return false;
					}					
				},error: function(ts) {
					console.log(ts);
					formSubmitted = false;
					swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
					return false;
				}
			});
		}
	//}
});

function getCustomers(customer_id){
	if(customer_id != ''){		
		$.ajax({
			type: 'POST',
			data: {customer_id:customer_id},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: getCustomersDataURL,
			success: function(data){
				$('#customer_name').html(data);
				$('#customer_name').trigger('change');
			},error: function(ts) {
				console.log(ts);
				formSubmitted = false;
				swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
				return false;
			}
		});
	}
}




$(document).ready(function(){
    var formSubmitted = false;
	$('#form_submit_order, #form_submit_order1').click(function(e){	
		var flag = 0;
        var submitBTN = $(this).attr('id');
        if(!formSubmitted){
            formSubmitted = false;
            $('#edit_order_modal').modal('hide');			
			
            if($.trim($("#customer_id_value").val()) == ''){
                //flag = 1;
                //swal("Error!", 'Please Enter Customer Information.', "error");
               // return false;
            }
			if($.trim($("#name").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Customer name.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#email").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Customer Email.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#mobile").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Customer Contact.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			/*if($.trim($("#address").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Customer Address.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			
			if($.trim($("#country").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Select Country.', "error");
				return false;
			}
			if($.trim($("#state").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Select State.', "error");
				return false;
			}
			if($.trim($("#city").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Select City.', "error");
				return false;
			}
			if($.trim($("#zipcode").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Select Zipcode.', "error");
				return false;
			}*/
			
			if($.trim($("#billing_address").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Billing Address.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#billing_country").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Billing Country.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#billing_state").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Billing State.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#billing_city").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Billing City.', "error");
				return false;
			}
			if($.trim($("#billing_zipcode").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Billing Zipcode.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			
			if($.trim($("#shipping_address").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Shiping Address.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#shipping_country").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Shipping Country.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#shipping_state").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Shipping State.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#shipping_city").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Shippping City.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			if($.trim($("#shipping_zipcode").val()) == ''){
				flag = 1;
				swal("Error!", 'Please Enter Shipping Zipcode.', "error");
				$('#edit_order_modal').modal('show');
				return false;
			}
			var pid_value = null;
			$('.product_id').each(function(key,value){				
				if(this.value > 0){
					pid_value = this.value;
					return false;
				}
            });
			
			if(pid_value == null){
                flag = 1;
                swal("Error!", 'Please Enter Product Name.', "error");
                return false;
            }
			
			if($.trim($("#notes").val()) == ''){
				//flag = 1;
				//swal("Error!", 'Please Enter Notes.', "error");
				//return false;
			}
			
            if($.trim($("#payment_method_type").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Select Payment Method Type.', "error");
                return false;
            }
            if($.trim($("#payment_method").val()) == ''){
                flag = 1;
                swal("Error!", 'Please Select Payment Method.', "error");
                return false;
            }
			

            if(flag == 0){

                $('#'+submitBTN+' .indicator-label').addClass('d-none');
                $('#'+submitBTN+' .indicator-progress').removeClass('d-none');

                var form = $('#orderForm')[0];
                var formData = new FormData(form);
                formSubmitted = true;
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: saveOrderURL,
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
                                    window.location.assign(listDataURL);
                                }else{
                                    window.location.assign(returnURL);
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
