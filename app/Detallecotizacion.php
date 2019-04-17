<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detallecotizacion extends Model
{
    protected $table = 'detallecotizacion';

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

    public function detalles()
    {
        return $this->hasMany('App\Detallecotizacion');
    }    
}
