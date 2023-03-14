<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $table = 'agency';
    protected $primaryKey = 'agency_id';
    protected $guarded = ['agency_id'];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class,'agency_id','agency_id');
    }

    public function stores()
    {
        return $this->hasMany(Store::class,'agency_id','agency_id');
    }

    public function customers()
    {
        return $this->hasMany(User::class,'agency_id','agency_id');
    }
}
