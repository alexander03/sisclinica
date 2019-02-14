<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Detallehistoriacie extends Model
{
	//use SoftDeletes;
    protected $table = 'detallehistoriacie';
    //protected $dates = ['deleted_at'];

    public function historiaclinica()
    {
        return $this->belongsTo('App\HistoriaClinica', 'historiaclinica_id');
    }

    public function cie()
    {
        return $this->belongsTo('App\Cie', 'cie_id');
    }
   
}
