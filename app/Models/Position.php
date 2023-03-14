<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = "employee_position";
    protected $primaryKey = "position_id";
    protected $guarded = "position_id";
}
