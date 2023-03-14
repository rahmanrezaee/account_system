<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table='company';
    protected $primaryKey='company_id';
    protected $fillable=['company_name','phone','address','status'];
    public $timestamps=false;


    public function buyfactors(){

        return $this->hasMany(BuyFactor::class,'company_id','company_id');
    }

}
