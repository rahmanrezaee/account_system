<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table='currency';
    protected $primaryKey='currency_id';
    protected $guarded=['currency_id'];
    public $timestamps=false;

    public function moneyTransfers()
    {
        return $this->hasMany(TransferMoney::class, 'currency_id');
    }

    public function MoneyStores(){
        return $this->hasMany(StoreMoney::class, 'currency_id');
    }
}
