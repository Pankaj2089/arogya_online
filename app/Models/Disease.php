<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $table = 'diseases';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'dept_id',
        'status',
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
