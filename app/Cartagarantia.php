<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Cartagarantia extends Model
{
    use SoftDeletes;
    protected $table = 'cartagarantia';
    protected $dates = ['deleted_at'];    

    public function cotizacion()
    {
        return $this->belongsTo('App\Cotizacion', 'cotizacion_id');
    }

    public function responsable()
    {
        return $this->belongsTo('App\Person', 'responsable_id');
    }

    public function scopeNumeroSigue($query){
        $rs=$query->get();
        return str_pad(count($rs)+1,8,'0',STR_PAD_LEFT);    
    }
}
