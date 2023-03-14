<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TotalPayment extends Model
{
    protected $table='total_payment';
    protected $primaryKey='total_payment_id';
    protected $guarded=['total_payment_id'];
    public $timestamps=false;
}
