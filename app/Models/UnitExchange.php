<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitExchange extends Model
{
    protected $table = 'unit_exchange';
    protected $primaryKey = 'unit_exchanger_id';
    protected $guarded = ['unit_exchanger_id'];
    public $timestamps = false;
}
