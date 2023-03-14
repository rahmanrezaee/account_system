<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreMoney extends Model
{
    protected $table='money_store';
    protected $primaryKey='store_id';
    protected $guarded=['store_id'];
    public $timestamps=false;

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'currency_id');
    }

    public function transferMoneySenders()
    {
        return $this->hasMany(TransferMoney::class,'sender_id','store_id');
    }

    public function transferMoneyReceivers()
    {
        return $this->hasMany(TransferMoney::class,'receiver_id','store_id');
    }

    public function catchMoney()
    {
        return $this->hasMany(CatchMoney::class, 'account_type','store_id');
    }

    public function addMoney()
    {
        return $this->hasMany(AddMoney::class, 'account_type','store_id');
    }

    public function revenue()
    {
        return $this->hasOne(Revenue::class, 'account_type','store_id');
    }
}
