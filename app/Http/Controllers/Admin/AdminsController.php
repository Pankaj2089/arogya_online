<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Symptoms;
use App\Models\Ailments;
use App\Models\User;
use App\Models\Department;
use App\Models\Specialties;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use ReallySimpleJWT\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Session;
use Validator;
use Mail;
use URL;
use Cookie;
use Illuminate\Validation\Rule;

class AdminsController extends Controller{
	
	private static $User;
	public function __construct(){
		self::$User = new User();
	}
	
    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}		
        return view('/admin/admins/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$User->with('department')->where('status', 1)->whereIn('type', ['Admin', 'Operator', 'Doctor'])->where('id','!=',1);
        if (admin_dept_id()) {
            $query->where('dept_id', admin_dept_id());
        }
		if($request->input('name')  && $request->input('name') != ""){
            $name = $request->input('name');
            $query->where('name', 'like', '%'.$name.'%');
		}
		if($request->input('email')  && $request->input('email') != ""){
            $email = $request->input('email');
            $query->where('email', 'like', '%'.$email.'%');
		}
		if($request->input('mobile')  && $request->input('mobile') != ""){
            $mobile = $request->input('mobile');
            $query->where('mobile', 'like', '%'.$mobile.'%');
		}

		$records =  $query->orderBy('id', 'DESC')->paginate(20);
        return view('/admin/admins/paginate',compact('records'));
    }

    #add new Service Type
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'type' => 'required|in:Operator,Doctor',
                'dept_id' => 'required_if:type,Doctor|nullable|exists:departments,id',
                'name' => 'required',
				'email' => 'required|email|unique:users,email',
                'mobile' => 'required|min:10|numeric',
				'password' => 'required'
            ],[
                'type.required' => 'Please select type.',
                'type.in' => 'Please select a valid type.',
                'dept_id.required_if' => 'Please select department for Doctor.',
                'dept_id.exists' => 'Please select a valid department.',
                'name.required' => 'Please enter name.',
				'email.required' => 'Please enter email.',
				'email.email' => 'Please enter valid email.',
				'email.unique' => 'Email already exists.',
                'mobile.required' => 'Please enter contact number.',
                'mobile.min' => 'Please enter valid contact number.',
				'mobile.numeric' => 'Please enter valid contact number.',
				'password.required' => 'Please enter password.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('type')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('type')));die;
				}
				if($errors->first('dept_id')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('dept_id')));die;
				}
				if($errors->first('name')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('name')));die;
				}
				if($errors->first('email')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('email')));die;
				}
                if($errors->first('mobile')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('mobile')));die;
				} 
				if($errors->first('password')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('password')));die;
				}                
			}else{
                if(!self::$User->ExistingRecord($request->input('email'))){
                    $setData['type'] = $request->input('type');
                    $setData['dept_id'] = $request->input('type') === 'Doctor' && $request->input('dept_id') ? (int) $request->input('dept_id') : null;
                    $setData['name'] = $request->input('name');
					$setData['email'] = $request->input('email');
					$setData['mobile'] = $request->input('mobile');
                    $password = password_hash($request->post('password'),PASSWORD_BCRYPT);
                  	$setData['password'] = $password;
					
                    $record = self::$User->CreateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Admin details added successfully'));die;
			}
		}
		$departments = Department::where('status', 1)->orderBy('name')->get(['id', 'name']);
		return view('/admin/admins/add-page', compact('departments'));
    }

    #edit Service Type
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'type' => 'required|in:Admin,Operator,Doctor',
                'dept_id' => 'required_if:type,Doctor|nullable|exists:departments,id',
                'name' => 'required',
				'email' => 'required|email|unique:users,email,'.$RowID,
                'mobile' => 'required|min:10|numeric',
				//'password' => 'required'
            ],[
                'type.required' => 'Please select type.',
                'type.in' => 'Please select a valid type.',
                'dept_id.required_if' => 'Please select department for Doctor.',
                'dept_id.exists' => 'Please select a valid department.',
                'name.required' => 'Please enter name.',
				'email.required' => 'Please enter email.',
				'email.email' => 'Please enter valid email.',
				'email.unique' => 'Email already exists.',
                'mobile.required' => 'Please enter contact number.',
                'mobile.min' => 'Please enter valid contact number.',
				'mobile.numeric' => 'Please enter valid contact number.',
				//'password.required' => 'Please enter passwords.'
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('type')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('type')));die;
				}
				if($errors->first('dept_id')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('dept_id')));die;
				}
				if($errors->first('name')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('name')));die;
				}
				if($errors->first('email')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('email')));die;
				}
                if($errors->first('mobile')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('mobile')));die;
				}
				if($errors->first('password')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('password')));die;
				}
			}else{
                if(self::$User->ExistingRecordUpdate($request->input('email'), $RowID)){
                    echo json_encode(array('heading'=>'Error','msg'=>'Admin details already exists.'));die;
                }else{
                    $setData['id'] =  $RowID;
					$setData['type'] = $request->input('type');
					$setData['dept_id'] = $request->input('type') === 'Doctor' && $request->input('dept_id') ? (int) $request->input('dept_id') : null;
					$setData['name'] = $request->input('name');
					$setData['email'] = $request->input('email');
					$setData['mobile'] = $request->input('mobile');
					if($request->post('password') && !empty($request->post('password'))){
						$password = password_hash($request->post('password'),PASSWORD_BCRYPT);
						$setData['password'] = $password;
					}
                    self::$User->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Admin details updated successfully'));die;
			}
		}
		$rowData = self::$User->where(array('id' => $RowID))->first();
		$departments = Department::where('status', 1)->orderBy('name')->get(['id', 'name']);
        if(isset($rowData->id)){
            return view('/admin/admins/edit-page',compact('rowData','row_id','departments'));
        }else{
            return redirect('/admin/admins');
        }
    }
	
}
