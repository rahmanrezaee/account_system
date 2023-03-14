<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyProduct extends Model
{
    protected $table='buy_product';
    protected $primaryKey='buy_product_id';
    protected $guarded='buy_product_id';
    public $timestamps=false;
    public function product(){

        return $this->belongsTo(BuyProduct::class,'product_id','product_id');
    }
}
