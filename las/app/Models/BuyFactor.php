<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyFactor extends Model
{

    protected $table='buy_factor';
    protected $guarded='buy_factor_id';
    protected $primaryKey='buy_factor_id';
    public $timestamps=false;
    public function company(){
        return $this->belongsTo(Company::class,'company_id','company_id');
    }
    public function stack(){
        return $this->belongsTo(Store::class,'store_id','store_id');
    }
}
