<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyExchanger extends Model
{
    protected $table = 'currency_exchange';
    protected $primaryKey = 'currency_exch_id';
    protected $guarded = ['currency_exch_id'];
    public $timestamps = false;
}
