<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
    ];

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

    public function ExistingRecord($name)
    {
        return $this::where('name', $name)->exists();
    }

    public function ExistingRecordUpdate($name, $id)
    {
        return $this::where('name', $name)->where('id', '!=', $id)->exists();
    }
}
