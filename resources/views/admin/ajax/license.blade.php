<option value="">Select License</option>
@foreach($licenses as $key => $license)
<option value="{{$license->id}}">{{$license->title}}</option>
@endforeach