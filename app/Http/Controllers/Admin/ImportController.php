<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use App\Models\ShippingMethods;
use App\Models\PaymentMethods;
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
use App\Models\Reviews;


class ImportController extends Controller{
	
	private static $User;	
		
	public function __construct(){
		self::$User = new User();		
	}
	
	#importCustomer
    public function importCustomer(Request $request){
		
		if(!$request->session()->has('admin_email')){ echo 'SessionExpired'; die; }
		
		$fileName = $_FILES["file"]["tmp_name"];
		if(isset($fileName) && !empty($fileName)){
			$csvMimes = array('application/csv', 'text/csv');
			if(!empty($_FILES['file']['name']) && $_FILES["file"]["size"] > 0 && in_array($_FILES['file']['type'], $csvMimes)){
				$file = fopen($fileName, "r");
				$num = 1;
				$error = NULL;
				$error = array();
				while(($column = fgetcsv($file, 10000, ",")) !== FALSE){
					if($num > 1){
						$gender_array = array('Male','Female','Other');
						$count = self::$User->where('email',trim($column[2]))->count();
						if($count == 0){
							$setData['type'] = 'User';
							$setData['name'] = $column[1];
							$setData['email'] = $column[2];
							$setData['mobile'] = $column[3];
							if(in_array($column[4],$gender_array)){
								$setData['gender'] = $column[4];	
							}else{
								$error['gender'] = $column[4];
							}						
							if(count($error) == 0){
								$record = self::$User->CreateRecord($setData);
							}
						}
					}
					$num++;
				}
				echo 'Success'; die;
			}else{
				echo 'InvalidFileType'; die;
			}
		}else{
			echo 'ChoseFile'; die;
		}
    }	
	
}