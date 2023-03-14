<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reason_Pay extends Model
{
    protected $table='expense_reason';
    protected $guarded=['expense_reason_id'];
    public $timestamps=false;

    public function expenses()
    {
        return $this->hasMany(Expense::class,'expense_reason_id','expense_reason_id');
    }
}
