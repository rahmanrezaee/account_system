<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $table='owner';
    protected $primaryKey='owner_id';
    protected $guarded=['owner_id'];
    public $timestamps=false;
}
