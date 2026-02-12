<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdRegistration extends Model
{
    protected $table = 'ipd_registration';

    public $timestamps = false;

    protected $fillable = [
        'opd_registration_id',
        'ipd_number',
        'patient_name',
        'patient_age',
        'patient_age_unit',
        'gender',
        'opd_number',
        'hid_number',
        'dept_id',
        'category',
        'date',
        'fath_husb_name',
        'address',
        'diagnosis',
        'bed_distribution_id',
        'admit_by_user_id',
        'amount',
        'discharge_date',
        'discharge_dept_id',
        'discharge_type',
    ];

    protected $casts = [
        'date' => 'date',
        'discharge_date' => 'date',
    ];

    public function opdRegistration()
    {
        return $this->belongsTo(OpdRegistration::class, 'opd_registration_id');
    }

    public function bedDistribution()
    {
        return $this->belongsTo(BedDistribution::class, 'bed_distribution_id');
    }

    public function admitByUser()
    {
        return $this->belongsTo(User::class, 'admit_by_user_id');
    }

    public function dischargeDepartment()
    {
        return $this->belongsTo(Department::class, 'discharge_dept_id');
    }
}
