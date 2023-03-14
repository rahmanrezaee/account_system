<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'options';
    protected $primaryKey = 'options_id';
    protected $guarded = ['options_id'];
    public $timestamps = false;
}