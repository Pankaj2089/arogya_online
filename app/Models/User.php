<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'dept_id',
        'name',
        'email',
        'password',
		'otp',
		'mobile',
		'gender',
		'dob',
		'address',
		'city',
		'zipcode',
		'emergency_contact_name',
		'emergency_contact_number',
		'emergency_contact_relation_id',
        'photo',
        'address',
		'state',
		'country',
        'billing_details',
        'language',
        'tax_exempt',
        'last_login',
		'shipping_method_id',
        'is_delete',
		'current_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
		'otp'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public function department(){
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function ExistingRecord($email){
		return $this::where('email',$email)->where('status','!=', 3)->exists();
	}
	public function ExistingRecordUpdate($email, $id){
		return $this::where('email',$email)->where('id','!=', $id)->where('status','!=', 3)->exists();
	}

    public function getUsersNames($ids){
        $user_name = 'N/A';
		$userIDs = explode(',',$ids);
        $users = $this::whereIn('id',$userIDs)->get();
        if(count($users) > 0){
            $usersArr = [];
            foreach($users as $user){
                $usersArr[] = $user->name;
            }
            if(count($usersArr) > 0){
                $user_name = implode(', ',$usersArr);
            }
            if(count($usersArr) > 1){
                $user_name = substr_replace($user_name, ' and', strrpos($user_name, ','), 1);
            }
        }
        return $user_name;
    }
}
