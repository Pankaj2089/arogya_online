<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedDistribution extends Model
{
    protected $table = 'bed_distributions';

    public $timestamps = false;

    protected $fillable = [
        'department_id',
        'gender',
        'bed_no',
        'bed_status',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
