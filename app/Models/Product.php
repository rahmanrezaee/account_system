<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table='product';
    protected $guarded=['product_id'];
    protected $primaryKey='product_id';
    public $timestamps=false;


    public function buyProducts(){

        return $this->hasMany(BuyProduct::class,'product_id','product_id');
    }
}
