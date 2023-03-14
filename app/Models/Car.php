<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'car_revenue';
    protected $primaryKey = 'car_revenue_id';
    protected $guarded = ['car_revenue_id'];
    public $timestamps = false;
}
