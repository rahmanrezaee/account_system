<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoneyStore extends Model
{
   protected  $table = 'money_store';
   protected $guarded =['store_id'];
   protected $primaryKey='store_id';
   protected $fillable = ['store_id'];
   public $timestamps = false;
}
