<option value="">Select City</option>
@php
	if(isset($cities) && !empty($cities)){
    	foreach($cities as $key => $city){
@endphp
        	<option value="{{$key}}">{{$city}}</option>
@php    }
   	} die;
@endphp