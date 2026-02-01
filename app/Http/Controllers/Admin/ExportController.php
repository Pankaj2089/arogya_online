<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
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

class ExportController extends Controller{

	public function __construct(){
	}

}