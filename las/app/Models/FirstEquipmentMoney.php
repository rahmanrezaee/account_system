<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirstEquipmentMoney extends Model
{
    protected $table = 'first_equipment_money';
    protected $primaryKey = 'first_money_eq_id';
    protected $guarded = ['first_money_eq_id'];
    public $timestamps = false;
}
