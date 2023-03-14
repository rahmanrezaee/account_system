<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $table='customer_payment';
    protected $primaryKey='customer_payment_id';
    protected $guarded=['customer_payment_id'];
    public $timestamps=false;


    public function getTableNameAttribute()
    {

        return "customer_payment";

    }


}
