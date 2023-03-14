<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table='product_unit';
    protected $primaryKey='unit_id';
    protected $guarded=['unit_id'];
    public $timestamps=false;
}
