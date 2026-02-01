<option value="">Select State</option>
@php
	if(isset($states) && !empty($states)){
    	foreach($states as $key => $state){
@endphp
        	<option value="{{$key}}">{{$state}}</option>
@php    }
   	} die;
@endphp