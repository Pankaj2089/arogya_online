<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use ReallySimpleJWT\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Languages;
use Session;
use Validator;
use Mail;
use URL;
use Cookie;
use Illuminate\Validation\Rule;

class UsersController extends Controller{

	private static $User;
    private static $TokenHelper;
	private static $Orders;
	public function __construct(){
		self::$User = new User();
        self::$TokenHelper = new TokenHelper();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        return view('/admin/users/index');
    }

    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$User->where('status', '!=', 3)->where('type', 'User');

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
        return view('/admin/users/paginate',compact('records'));
    }

    #add new Service Type
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'name' => 'required',
				'email' => 'required|email|unique:users,email',
                'mobile' => 'required|min:10'
            ],[
                'name.required' => 'Please enter name.',
				'email.required' => 'Please enter email.',
				'email.email' => 'Please enter valid email.',
				'email.unique' => 'Email already exists.',
                'mobile.required' => 'Please enter contact number.',
                'mobile.min' => 'Please enter valid contact number.'
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
			}else{
                if(!self::$User->ExistingRecord($request->input('email'))){
                    $setData['type'] = 'User';
                    $setData['name'] = $request->input('name');
					$setData['email'] = $request->input('email');
					$setData['mobile'] = $request->input('mobile');
					$setData['gender'] = $request->input('gender');
					$setData['address'] = $request->input('address');
					$setData['city'] = $request->input('city');
					$setData['zipcode'] = $request->input('zipcode');

                    if(isset($request->profile_image) && $request->profile_image->extension() != ""){
                        $validator = Validator::make($request->all(), [
                            'profile_image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:2048'
                        ]);
                        if($validator->fails()){
                            $errors = $validator->errors();
                            return json_encode(array('heading'=>'Error','msg'=>$errors->first('profile_image')));die;
                        }else{
							$actual_image_name = strtolower(sha1(str_shuffle(microtime(true).mt_rand(100001,999999)).uniqid(rand().true).$request->file('profile_image')).'.'.$request->profile_image->extension());
                            $destination = base_path().'/public/img/users/';
                            $request->profile_image->move($destination, $actual_image_name);
                            $setData['photo'] = $actual_image_name;
                        }
                    }
                    $record = self::$User->CreateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Customer added successfully'));die;
			}
		}
		return view('/admin/users/add-page');
    }

    #edit Service Type
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'name' => 'required',
				'email' => 'required|email|unique:users,email,'.$RowID,
                'mobile' => 'required|min:10',
            ],[
                'name.required' => 'Please enter name.',
				'email.required' => 'Please enter email.',				
				'email.email' => 'Please enter valid email.',
				'email.unique' => 'Email already exists.',
                'mobile.required' => 'Please enter contact number.',
                'mobile.min' => 'Please enter valid contact number.',
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
			}else{
                //profile image
                if(self::$User->ExistingRecordUpdate($request->input('email'), $RowID)){
                    echo json_encode(array('heading'=>'Error','msg'=>'Customer already exists.'));die;
                }else{
                    $setData['id'] =  $RowID;
                    $setData['name'] = $request->input('name');
					$setData['email'] = $request->input('email');
					$setData['mobile'] = $request->input('mobile');
					$setData['gender'] = $request->input('gender');
					$setData['address'] = $request->input('address');
					$setData['city'] = $request->input('city');
					$setData['zipcode'] = $request->input('zipcode');
                    $setData['emergency_contact_name'] = $request->input('emergency_contact_name');
					$setData['emergency_contact_number'] = $request->input('emergency_contact_number');
                    if(isset($request->profile_image) && $request->profile_image->extension() != ""){
                        $validator = Validator::make($request->all(), [
                            'profile_image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:2048'
                        ]);
                        if($validator->fails()){
                            $errors = $validator->errors();
                            return json_encode(array('heading'=>'Error','msg'=>$errors->first('profile_image')));die;
                        }else{
							$actual_image_name = strtolower(sha1(str_shuffle(microtime(true).mt_rand(100001,999999)).uniqid(rand().true).$request->file('profile_image')).'.'.$request->profile_image->extension());
                            $destination = base_path().'/public/img/users/';
                            $request->profile_image->move($destination, $actual_image_name);
                            $setData['photo'] = $actual_image_name;
                            if($request->input('old_profile_image') != ""){
                                if(file_exists($destination.$request->input('old_profile_image'))){
                                    unlink($destination.$request->input('old_profile_image'));
                                }
                            }
                        }
                    }
                    self::$User->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Customer updated successfully'));die;
			}
		}
		$rowData = self::$User->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            if(!empty($rowData->billing_details)){
                $rowData->billing_details = json_decode($rowData->billing_details, true);
            }
            return view('/admin/users/edit-page',compact('rowData','row_id'));
        }else{
            return redirect('/admin/users');
        }
    }
	
}