<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
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

class AdminController extends Controller{
	
	private static $UserModel;
    private static $TokenHelper;
	public function __construct(){
		self::$UserModel = new AdminUser();
        self::$TokenHelper = new TokenHelper();
	}
	
    # admin login page
    public function login(Request $request){
        if($request->session()->has('admin_email')){return redirect('/admin/dashboard/');}
        $cookieUsername = Cookie::get('cookieUsername');
        $cookiePassword = Cookie::get('cookiePassword');
        return view('/admin/login',compact('cookieUsername','cookiePassword'));
    }

    # admin dashboard page
    public function admin_login(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required',
		],[
			'email.required' => 'Please enter your email address.',
			'email.email' => 'Please enter valid email address.',
			'password.required' => 'Please enter your password.'
		]);

		if($validator->fails()){
			 $errors = $validator->errors();
			if($errors->first('email')){
				echo json_encode(array('heading'=>'Error Email','msg'=>$errors->first('email')));
			}else if($errors->first('password')){
				echo json_encode(array('heading'=>'Error Password','msg'=>$errors->first('password')));
			}
		}else{
			if(isset($request->reminderMe) && $request->reminderMe == 1){
                Cookie::queue('cookieUsername', $request->username, 5000);
                Cookie::queue('cookiePassword', $request->password, 5000);
            }else{
                Cookie::queue('cookieUsername', '', 5000);
                Cookie::queue('cookiePassword', '', 5000);
            }

			$User = self::$UserModel->where(array('email' => $request->email))->first();
			if($User){
				if($User->type == 'Admin' || $User->type == 'Account'){
					$PasswordMatch = password_verify($request->password, $User->password);
					if(!$PasswordMatch){
						echo json_encode(array('heading'=>'Error Account','msg'=>'Username and password incorrect'));
					}else{
						session(['admin_id' => $User->id, 'admin_email' => $User->email, 'admin_profile' => $User, 'admin_type' => $User->type, 'admin_name' => $User->name]);
						echo json_encode(array('heading'=>'Success','msg'=>''));
					}
				}else{
					echo json_encode(array('heading'=>'Error Account','msg'=>'Username and password incorrect'));
				}
			}else{
				echo json_encode(array('heading'=>'Error Account','msg'=>'Username and password incorrect'));
			}
		}
    }

    # admin dashboard page
    public function dashboard(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        return view('/admin/dashboard');
    }

    # admin dashboard page
    public function logout(Request $request){
        $request->session()->flush();
        return redirect('/admin/');
   }

}