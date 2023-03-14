<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale_Product extends Model{
    //
    protected $table='sale_product';
    protected $primaryKey='sale_id';
    protected $guarded=['sale_id'];
    public $timestamps=false;
}
