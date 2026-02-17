<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietPlans extends Model
{
    protected $table = 'diet_plans';

    public $timestamps = false;

    protected $fillable = [ 
        'ipd_no',
        'opd_no',
        'patient_name',
        'gendar',
        'morning',
        'afternoon',
        'evening',
        'plan_date',
        'dept_id'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function GetRecordById($id)
    {
        return $this::where('id', $id)->first();
    }

    public function UpdateRecord($Details)
    {
        $this::where('id', $Details['id'])->update($Details);
        return true;
    }

    public function CreateRecord($Details)
    {
        return $this::create($Details);
    }

    public function ExistingRecord($name, $dept_id)
    {
        return $this::where('name', $name)->where('dept_id', $dept_id)->exists();
    }

    public function ExistingRecordUpdate($name, $dept_id, $id)
    {
        return $this::where('name', $name)->where('dept_id', $dept_id)->where('id', '!=', $id)->exists();
    }
}
