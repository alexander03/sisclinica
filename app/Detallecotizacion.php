<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Detallecotizacion extends Model
{
	 use SoftDeletes;
    protected $table = 'detallecotizacion';
    protected $dates = ['deleted_at'];

    public function cotizacion()
    {
        return $this->belongsTo('App\Cotizacion', 'cotizacion_id');
    }
    
    public function servicio()
    {
        return $this->belongsTo('App\Servicio', 'servicio_id');
    }

 	public function doctor()
    {
        return $this->belongsTo('App\Person', 'doctor_id');
    }    
}
