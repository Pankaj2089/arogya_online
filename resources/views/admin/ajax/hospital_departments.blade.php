<option value="">Select Hospital Department</option>
@if(count($HospitalDepartments) > 0)
    @foreach($HospitalDepartments as $key => $HospitalDepartment)
    <option value="{{$HospitalDepartment->id}}">{{$HospitalDepartment->name}}</option>
    @endforeach
@endif

