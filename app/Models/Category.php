<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
   
    protected $table='category';
    protected $primaryKey='category_id';
    protected $guarded=['category_id'];
    public $timestamps=false;
}
