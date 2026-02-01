<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Settings extends Authenticatable{

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "admin_email",
        "company_name",
		"business_address",
        "mobile",
		"whatsapp",
		"footer_content",
		"footer_info",
		"logo",
    ];

    protected $UpdatableFields = [
        "admin_email",
        "company_name",
		"business_address",
        "mobile",
		"whatsapp",
		"footer_content",
		"footer_info",
		"logo",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    public function GetRecordById($id){
		return $this::where('id', $id)->first();
	}
	
	public function UpdateRecord($Details){
		$Record = $this::where('id', $Details['id'])->update($Details);
		return true;
	}
	
	public function CreateRecord($Details){
		$Record = $this::create($Details);
		return $Record;
	}

}