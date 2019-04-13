<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Respaldo extends Model
{
    use SoftDeletes;
    protected $table = 'respaldo';
    protected $dates = ['deleted_at'];    

    public function responsable()
    {
        return $this->belongsTo('App\Person', 'responsable_id');
    }

    public function scopeNumeroSigue($query, $estado){
        $rs=$query->where('estado', '=', $estado)->get();
        return str_pad(count($rs)+1,8,'0',STR_PAD_LEFT);    
    }
}
