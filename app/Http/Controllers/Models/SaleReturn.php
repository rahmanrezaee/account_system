<?php

namespace App\Http\Controllers\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $table = 'sales_return';
    protected $primaryKey = 'return_id';
    protected $guarded = ['return_id'];
    public $timestamps = false;


}
