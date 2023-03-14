<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale_Factor extends Model
{
    protected $table='sale_factor';
    protected $primaryKey='sale_factor_id';
    protected $guarded=['sale_factor_id'];
    public $timestamps=false;


}
