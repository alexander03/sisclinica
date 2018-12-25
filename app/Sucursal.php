<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Sucursal extends Model
{
    use SoftDeletes;
    protected $table = 'Sucursal';
    protected $dates = ['deleted_at'];
    
}
