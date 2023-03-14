<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatchMoney extends Model
{
    protected $table = 'catch_money';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function moneyStore()
    {
        return $this->belongsTo(StoreMoney::class, 'account_type','store_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id','employee_id');
    }
}
