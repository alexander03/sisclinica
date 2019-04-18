<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hojacosto extends Model
{
	 use SoftDeletes;
    protected $table = 'hojacosto';
    protected $dates = ['deleted_at'];

    public function hospitalizacion(){
        return $this->belongsTo('App\Hospitalizacion', 'hospitalizacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Person', 'usuario_id');
    }
}
