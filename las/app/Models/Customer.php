<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $table='customer';
    protected $primaryKey='customer_id';
    protected $guarded=['customer_id'];
    public $timestamps=false;


}
