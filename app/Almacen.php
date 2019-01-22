<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Almacen extends Model
{
	 use SoftDeletes;
    protected $table = 'almacen';
    protected $dates = ['deleted_at'];
}
