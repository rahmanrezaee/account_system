<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferMoney extends Model
{
    protected $table='transfer_money';
    protected $primaryKey='transfer_id';
    protected $guarded=['transfer_id'];
    public $timestamps=false;

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'currency_id');
    }


    public function storeMoneySender()
    {
        return $this->belongsTo(StoreMoney::class, 'sender_id', 'store_id');
    }

    public function storeMoneyReceiver()
    {
        return $this->belongsTo(StoreMoney::class, 'receiver_id', 'store_id');
    }
}
