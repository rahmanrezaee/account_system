<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class CustomerPaymentReport extends Model
{
    protected $table='customer_payment';
    protected $primaryKey='customer_payment_id';
    protected $guarded=['customer_payment_id'];
    public $timestamps=false;
}
