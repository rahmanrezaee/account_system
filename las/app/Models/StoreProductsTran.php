<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProductsTran extends Model
{

    protected $table='store_prod_transfer';
    protected $primaryKey='store_transfer_id';
    protected $guarded=['store_transfer_id'];
    public $timestamps=false;



}
