<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	
	public function builtSlug($input_lines){
        preg_match_all("/[0-9A-Za-z\s]/", trim($input_lines), $output_array);
        $slug = strtolower(preg_replace("/[\s]/", "-", join($output_array[0])));
        return preg_replace("/-{2,}/", "-", $slug);
    }
	
}
