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


var x = 2;
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
			$('.addMoreBtn').html('+ Add');
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
	var p_id = $('#product_id'+counter).val();
	//LoadProductInfo(p_id,counter);
	
	//if($('#product_id'+counter).val() > 0){
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
		
		var total_product = (parseFloat(price)*parseInt(qty));
		var totalPrice = parseFloat(total_product);
		$('#total'+counter).val(totalPrice.toFixed(2));				
		setTotal();	
	//}
}
function LoadProductInfo(value,counter){
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
				var sum = parseFloat(price);
				var total = parseFloat(sum);
				if(qty > 0 && qty <= 10){
					var $html = '';
					for(var i = 1; i <= qty; i++) {
					  $html+= '<option value="'+i+'">'+i+'</option>';
					}
					$('#product_qty'+counter).val($html);
					$('#product_qty'+counter).val(1);
				}
				$('#product_qty'+counter).val(1);
				$('#product_id'+counter).val(pid);				
				$('#product_code'+counter).val(product_code);
				$('#product_price'+counter).val(price);
				$('#product_tax'+counter).val(response.tax);
				$('#product_tax_include'+counter).val(response.is_tax_included);
				$('#product_package_cost'+counter).val(response.packaging_cost);
				$('#product_package_type'+counter).val(response.packaging_cost_time);
				$('#product_shipping_cost'+counter).val(response.shipping_cost);
				$('#product_shipping_cost_time'+counter).val(response.shipping_cost_time);
				$('#cost_depend_shipping'+counter).val(response.cost_depending_shipping);
				$('#weight_depend_shipping'+counter).val(response.weight_depending_shipping);
				$('#is_free_shipping'+counter).val(response.is_free_shipping);
				
				
				$('#total'+counter).val(total);
				
				setTotal();	
				
				return true;
			},error: function(ts) {
				console.log(ts);
				swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
				return false;
			}
		});
	}else{ return false; }
}
function selectProduct(value,counter,pCode,pName,vName){
	LoadProductInfo(value,counter);
	$('#search_products'+counter).hide();
	$('#product_name'+counter).hide();
	$('#p_selected_data'+counter).html(pName+'<br>'+pCode+'<br>'+vName);
}
/*$(document).on('change','#product_name',function(){	
	var value = $(this).val();
	var counter = $(this).attr('row_id');
	LoadProductInfo(value,counter);
});*/
function CustomSetting(){
	var subTotal = $('#subTotal').val();
	var packageCost = $('#packageCost').val();
	var tax = $('#tax').val();
	var shippingCost = $('#shippingCost').val();
	var shippingTax = $('#shippingTax').val();
	var codAmt = $('#codAmt').val();
	var discount = $('#discount').val();
	
	var grandTotal = (parseFloat(subTotal)+parseFloat(packageCost)+parseFloat(tax)+parseFloat(shippingCost)+parseFloat(shippingTax)+parseFloat(codAmt))-parseFloat(discount);
	
	$("#grandTotal").text(grandTotal.toFixed(2));
	$('#hiddenTotal').val(grandTotal.toFixed(2));
}
function setTotal(){		
	var gstchr = '';
	var grandTotal = 0;
	var totalTax = 0;
	var totalPackagingCost = 0;
	var totalShippingCost = 0;
	var finalTotal = 0;
	$("input[name='total[]']").each(function(){
		var rowCounter = $(this).attr('row_id');
		// price
		var firstAmt = parseFloat($(this).val());
		if($.isNumeric(firstAmt)){
			grandTotal = parseFloat(grandTotal)+parseFloat(firstAmt);
		}
		// tax
		if($('#product_tax_include'+rowCounter).val() == 'NO'){
			var taxAmt = parseFloat($('#product_price'+rowCounter).val())*parseFloat($('#product_tax'+rowCounter).val())/100;
			totalTax = parseFloat(totalTax)+parseFloat(taxAmt);
			totalTax = parseFloat(totalTax)*parseInt($('#product_qty'+rowCounter).val());
		}
		// packaging cost
		var packagCost = parseFloat($('#product_package_cost'+rowCounter).val());
		if($('#product_package_type'+rowCounter).val() == 'Multiple Time'){
			var packagCost = parseFloat($('#product_package_cost'+rowCounter).val())*parseInt($('#product_qty'+rowCounter).val());
		}
		totalPackagingCost = parseFloat(totalPackagingCost)+parseFloat(packagCost);
		
		// shiping cost
		if($('#is_free_shipping'+rowCounter).val() == 'NO'){
			var shippingCost = parseFloat($('#product_shipping_cost'+rowCounter).val());
			if($('#product_shipping_cost_time'+rowCounter).val() == 'Multiple Time'){
				var shippingCost = parseFloat($('#product_shipping_cost'+rowCounter).val())*parseInt($('#product_qty'+rowCounter).val());
			}
			totalShippingCost = parseFloat(totalShippingCost)+parseFloat(shippingCost)+parseFloat($('#cost_depend_shipping'+rowCounter).val())+parseFloat($('#weight_depend_shipping'+rowCounter).val());
		}

	});
	
	// COD charges
	var totalCod = 0;
	if($('#payment_method_type').val() == 'COD'){
		var codPercent = parseFloat(grandTotal)*parseFloat($('#cod_percent').val())/100;
		var codAmt = parseFloat($('#cod_amt').val());
		totalCod = codPercent+codAmt;
	}
	// ShippingTax
	var totalShippingTax = 0;
	if($('#shipping_tax_amt').val() > 0){
		var totalShippingTax = parseFloat(totalShippingCost)*parseFloat($('#shipping_tax_amt').val())/100;
	}
	
	$("#codAmt").val(totalCod.toFixed(2));
	$("#subTotal").val(grandTotal.toFixed(2));
	$('#tax').val(totalTax.toFixed(2));
	$('#packageCost').val(totalPackagingCost.toFixed(2));
	$('#shippingCost').val(totalShippingCost.toFixed(2));
	$('#shippingTax').val(totalShippingTax.toFixed(2));
	
	finalTotal = parseFloat(grandTotal)+parseFloat(totalTax)+parseFloat(totalPackagingCost)+parseFloat(totalShippingCost)+parseFloat(totalCod)+parseFloat(totalShippingTax);
	$("#grandTotal").text(finalTotal.toFixed(2));
	$('#hiddenTotal').val(finalTotal.toFixed(2));
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
			
			if($('#page_name').val() == 'AddOrder'){
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
