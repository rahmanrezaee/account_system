<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class employee_position extends Model
{
     protected  $table='employee_position';
    protected $primaryKey='position_id';
    protected $guarded=['position_id'];
    public  $timestamps=false;
    public function employee(){
 return $this->belongsTo(Employee::class,'position_id');
    }
}
