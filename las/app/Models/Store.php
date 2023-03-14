<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table='store';
    protected $primaryKey='store_id';
    protected $guarded=['store_id'];
    public $timestamps=false;

    public function buyFactores(){

        return $this->hasMany(BuyFactor::class,'store_id','store_id');
    }
    public function destroies(){

        return $this->hasMany(DestroyProduct::class,'store_id','store_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class,'agency_id', 'agency_id');
    }
}
