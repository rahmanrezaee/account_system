<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $table='employee';
    protected $guarded=['employee_id'];
    protected $primaryKey='employee_id';
    public $timestamps = false;


    public function catch_monies()
    {
        return $this->hasMany(CatchMoney::class,'employee_id','employee_id');
    }
}
