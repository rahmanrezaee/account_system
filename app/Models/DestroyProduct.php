<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestroyProduct extends Model
{
    protected $table='destroyed_product';
    protected $primaryKey='dest_pro_id';
    protected $guarded=['dest_pro_id'];
    public $timestamps=false;

    public function stack(){

        return $this->belongsTo(Store::class,'store_id','store_id');
    }
}
