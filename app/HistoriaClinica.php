<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class HistoriaClinica extends Model
{
    use SoftDeletes;
    protected $table = 'historiaclinica';
    protected $dates = ['deleted_at'];
    
    public function historia()
    {
        return $this->belongsTo('App\Historia', 'historia_id');
    }
    
    public function cie()
    {
        return $this->belongsTo('App\Cie', 'cie_id');
    }
    public function scopeNumeroSigue($query, $historia_id){
        $rs=$query->select(DB::raw("max((CASE WHEN numero IS NULL THEN 0 ELSE numero END)*1) AS maximo"))->where('historia_id', $historia_id)->first();
        return str_pad($rs->maximo+1,8,'0',STR_PAD_LEFT);    
    }

}
