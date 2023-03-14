<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_Store extends Model
{
    //
    protected $table='product_store';
    protected $primaryKey='product_stor_id';
    protected $fillable = ['store_id','product_id','quantity'];
    public $timestamps=false;
}
