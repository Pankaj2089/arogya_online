<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Symptoms;
use App\Models\Ailments;
use App\Models\User;
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

class AccountsController extends Controller{
	
	private static $User;
	public function __construct(){
		self::$User = new User();
	}
	
    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}		
        return view('/admin/accounts/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$User->where('status', '!=', 3)->where('type', 'Account');

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
        return view('/admin/accounts/paginate',compact('records'));
    }

    #add new Service Type
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'name' => 'required',
				'email' => 'required|email|unique:users,email',
                'mobile' => 'required|min:10|numeric',
				'password' => 'required'
            ],[
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
                    $setData['type'] = 'Account';
                    $setData['name'] = $request->input('name');
					$setData['email'] = $request->input('email');
					$setData['mobile'] = $request->input('mobile');
                    $password = password_hash($request->post('password'),PASSWORD_BCRYPT);
                  	$setData['password'] = $password;
					
                    $record = self::$User->CreateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Account details added successfully'));die;
			}
		}
		return view('/admin/accounts/add-page');
    }

    #edit Service Type
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'name' => 'required',
				'email' => 'required|email|unique:users,email,'.$RowID,
                'mobile' => 'required|min:10|numeric',
				//'password' => 'required'
            ],[
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
                    echo json_encode(array('heading'=>'Error','msg'=>'Account details already exists.'));die;
                }else{
                    $setData['id'] =  $RowID;
					$setData['name'] = $request->input('name');
					$setData['email'] = $request->input('email');
					$setData['mobile'] = $request->input('mobile');
					if($request->post('password') && !empty($request->post('password'))){
						$password = password_hash($request->post('password'),PASSWORD_BCRYPT);
						$setData['password'] = $password;
					}
                    self::$User->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Account details updated successfully'));die;
			}
		}
		$rowData = self::$User->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            return view('/admin/accounts/edit-page',compact('rowData','row_id'));
        }else{
            return redirect('/admin/accounts');
        }
    }
	
}