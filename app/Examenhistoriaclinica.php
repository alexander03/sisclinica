<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examenhistoriaclinica extends Model
{
	 use SoftDeletes;
    protected $table = 'examenhistoriaclinica';
    protected $dates = ['deleted_at'];

    public function historiaclinica()
    {
        return $this->belongsTo('App\Historiaclinica', 'historiaclinica_id');
    }

    public function servicio()
    {
        return $this->belongsTo('App\Servicio', 'servicio_id');
    }
}
