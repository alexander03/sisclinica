<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Cotizacion extends Model
{
    use SoftDeletes;
    protected $table = 'cotizacion';
    protected $dates = ['deleted_at'];    

    public function paciente()
    {
        return $this->belongsTo('App\Person', 'paciente_id');
    }

    public function responsable()
    {
        return $this->belongsTo('App\Person', 'responsable_id');
    }

    public function scopeNumeroSigue($query){
        $rs=$query->get();
        return str_pad(count($rs)+1,8,'0',STR_PAD_LEFT);    
    }

    public function plan()
    {
        return $this->belongsTo('App\Plan', 'plan_id');
    }

    public function detalles()
    {
        return $this->hasMany('App\Detallecotizacion');
    }
}
