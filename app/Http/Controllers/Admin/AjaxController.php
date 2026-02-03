<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
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

use App\Models\State;

class AjaxController extends Controller{
	
    private static $TokenHelper;
	public function __construct(){
        self::$TokenHelper = new TokenHelper();
	}

    public function changeStatus(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$tableName = $request->input('table');
		$rowID = $request->input('rowID');
		$status = $request->input('status');
		if($tableName != "" && $rowID != "" && $status != "" && is_numeric($rowID) && is_numeric($status)){
            if($tableName == 'pathy' && $status == 1){
                $categoryUsed = DB::table('practices')->where('pathy_id', $rowID)->where('status', 1)->count();
                if($categoryUsed > 0){
                    echo 'Cannot in-active this record (One or more practice are associated with this pathy.)';die;
                }
            }
            $newStatus = ($tableName == 'financial_years' || $tableName == 'departments' || $tableName == 'designations' || $tableName == 'diseases' || $tableName == 'bed_distributions') ? ($status == 1 ? 0 : 1) : ($status == 1 ? 2 : 1);
            if($tableName == 'financial_years' && $newStatus == 1){
                DB::table($tableName)->update(array('status' => 0));
            }
            $updateQuery = DB::table($tableName)->where('id', $rowID);
            if (function_exists('admin_dept_id') && admin_dept_id()) {
                $deptId = admin_dept_id();
                if ($tableName == 'bed_distributions') {
                    $updateQuery->where('department_id', $deptId);
                } elseif ($tableName == 'users') {
                    $updateQuery->where('dept_id', $deptId);
                } elseif ($tableName == 'diseases') {
                    $updateQuery->where('dept_id', $deptId);
                } elseif ($tableName == 'departments') {
                    $updateQuery->where('id', $deptId);
                }
            }
            $updateQuery->update(array('status' => $newStatus));
			echo 'Success';die;
		}else{
			echo 'InvalidData'; die;
		}
    }

    public function deleteRecord(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$tableName = $request->input('table');
		$rowID = $request->input('rowID');
		if($tableName != "" && $rowID != "" && is_numeric($rowID)){
            if($tableName == 'pathy'){
                $categoryUsed = DB::table('practices')->where('pathy_id', $rowID)->where('status','!=', 3)->count();
                if($categoryUsed > 0){
                    echo 'Cannot delete this record (One or more practices are associated with this pathy.)';die;
                }
            }
            if($tableName == 'financial_years'){
                DB::table($tableName)->where('id', $rowID)->delete();
            }elseif($tableName == 'designations'){
                $designationUsed = DB::table('doctor_profiles')->where('designation_id', $rowID)->count();
                if($designationUsed > 0){
                    echo 'Cannot delete this record (One or more records are associated with this designation.)';die;
                }
                DB::table($tableName)->where('id', $rowID)->delete();
            }elseif($tableName == 'departments' && function_exists('admin_dept_id') && admin_dept_id()){
                echo 'Cannot delete department.'; die;
            }elseif($tableName == 'departments'){
                $deptUsed = DB::table('diseases')->where('dept_id', $rowID)->count()
                    + DB::table('bed_distributions')->where('department_id', $rowID)->count()
                    + DB::table('doctor_profiles')->where('dept_id', $rowID)->count()
                    + DB::table('users')->where('dept_id', $rowID)->count();
                if($deptUsed > 0){
                    echo 'Cannot delete this record (One or more records are associated with this department.)';die;
                }
                DB::table($tableName)->where('id', $rowID)->delete();
            }elseif($tableName == 'bed_distributions'){
                $deleteQuery = DB::table($tableName)->where('id', $rowID);
                if (function_exists('admin_dept_id') && admin_dept_id()) {
                    $deleteQuery->where('department_id', admin_dept_id());
                }
                $ipdUsed = DB::table('ipd_registration')->where('bed_distribution_id', $rowID)->count();
                if($ipdUsed > 0){
                    echo 'Cannot delete this record (One or more IPD registrations use this bed.)';die;
                }
                $deleteQuery->delete();
            }elseif($tableName == 'users'){
                $userDeleteQuery = DB::table($tableName)->where('id', $rowID);
                if (function_exists('admin_dept_id') && admin_dept_id()) {
                    $userDeleteQuery->where('dept_id', admin_dept_id());
                }
                $userDeleteQuery->update(array('status' => 3));
            }elseif($tableName == 'diseases'){
                $deleteQuery = DB::table($tableName)->where('id', $rowID);
                if (function_exists('admin_dept_id') && admin_dept_id()) {
                    $deleteQuery->where('dept_id', admin_dept_id());
                }
                $deleteQuery->delete();
            }else{
                DB::table($tableName)->where(array('id' => $rowID))->update(array('status' => 3));
            }
			echo 'Success';die;
		}else{
			echo 'InvalidData'; die;
		}
    }

    public function productsChangeStatus(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$productIDs = $request->input('productIDs');
		$status = $request->input('status');
        if(count($productIDs) == 0){
            echo 'Please select Products.';die;
        }
		if($status != "" && is_numeric($status)){
            foreach($productIDs as $rowID){
                $newStatus = $status == 1 ? 2 : 1;
                DB::table('products')->where(array('id' => $rowID))->update(array('status' => $newStatus));
            }
			echo 'Success';die;
		}else{
			echo 'InvalidData'; die;
		}
    }

    public function productsDeleteRecord(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$productIDs = $request->input('productIDs');
		$status = $request->input('status');
        if(count($productIDs) == 0){
            echo 'Please select Products.';die;
        }
        foreach($productIDs as $rowID){
            $newStatus = $status == 1 ? 2 : 1;
            //DB::table('products')->where('id', $rowID)->delete();
            DB::table('products')->where(array('id' => $rowID))->update(array('status' => 3));
        }
        echo 'Success';die;

    }
	
	public function getState(Request $request){
		if($request->ajax()){
			$country_id = $request->input('countryId');
			$states = DB::table('states')->where('country_id',$country_id)->where('status',1)->orderBy('state')->pluck('state','id');

			echo view('/admin/ajax/get_state',compact('states'));
		}
		exit;
	}
	
	public function getCity(Request $request){
		if($request->ajax()){
			$state_id = $request->input('stateId');
			$cities = DB::table('cities')->where('state_id',$state_id)->where('status',1)->orderBy('city')->pluck('city','id');

			echo view('/admin/ajax/get_city',compact('cities'));
		}
		exit;
	}
	
	public function updateNewOrder(Request $request){
		if($request->Ajax()){
			$postData = $request->all();
			$msg = '';
			if(isset($postData) && !empty($postData)){				
				$actualVal = 0;
				$id = $request->input('id');
				$prev = $request->input('prev');
				$modal = $request->input('modal');
				$currval = $request->input('curval');
				
				$record =  DB::table($modal)->orderBy('ordering', 'DESC')->get('ordering')->first();
				$actualVal = $record->ordering;
				if($currval != 0 && is_numeric($currval)){
					//$data = DB::table($modal)->where(array('ordering' => $currval))->get()->first();
					#save current row
					DB::table($modal)->where('id',$id)->update(['ordering' => $currval]);
	
					#save previous row
					//DB::table($modal)->where('id',$data->id)->update(['ordering' => $prev]);
				}
				$msg = "success";			
			}			
			echo $msg;
		}
		exit;	
	}

	public function updateOrder(Request $request){
		if($request->Ajax()){
			$postData = $request->all();
			$msg = '';
			if(isset($postData) && !empty($postData)){				
				$actualVal = 0;
				$id = $request->input('id');
				$prev = $request->input('prev');
				$modal = $request->input('modal');
				$currval = $request->input('curval');
				
				$record =  DB::table($modal)->orderBy('ordering', 'DESC')->get('ordering')->first();
				$actualVal = $record->ordering;
				if($currval <= $actualVal && $currval != 0 && is_numeric($currval)){
					$data = DB::table($modal)->where(array('ordering' => $currval))->get()->first();
					#save current row
					DB::table($modal)->where('id',$id)->update(['ordering' => $currval]);
	
					#save previous row
					DB::table($modal)->where('id',$data->id)->update(['ordering' => $prev]);
				}
				$msg = "success";			
			}			
			echo $msg;
		}
		exit;	
	}

}