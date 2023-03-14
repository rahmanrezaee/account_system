<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddMoney extends Model
{
    protected $table = 'add_money';
    protected $primaryKey = 'add_money_id';
    protected $guarded = ['add_money_id'];
    public $timestamps = false;

    public function moneyStore()
    {
        return $this->belongsTo(StoreMoney::class, 'account_type','store_id');
    }
}
