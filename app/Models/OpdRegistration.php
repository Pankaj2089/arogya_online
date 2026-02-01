<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpdRegistration extends Model
{
    protected $table = 'opd_registration';

    public $timestamps = false;

    protected $fillable = [
        'financial_year_id',
        'patient_name',
        'fath_husb_name',
        'address',
        'date',
        'patient_age',
        'patient_age_unit',
        'gender',
        'dept_id',
        'register_type',
        'opd_number',
        'hid_number',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
}
