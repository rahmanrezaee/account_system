<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $table = 'revenue';
    protected $primaryKey = 'revenue_id';
    protected $guarded = ['revenue_id'];
    public $timestamps = false;


    public function store()
    {
        return $this->belongsTo(StoreMoney::class, 'account_type' ,'store_id');
    }

}
