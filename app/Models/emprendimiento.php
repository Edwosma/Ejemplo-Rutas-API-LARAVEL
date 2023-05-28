<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class emprendimiento extends Model
{
    use HasFactory;


    public function emprendedor()
    {
        return $this->belongsTo('App\Models\User','emprendedor_id');
    }

    // relacion uno a uno
    public function visualizaciones(){
        return $this->hasMany('App\Models\Visualizacion','emprendedimiento_id');
    }

     // relacion uno a uno
     public function comentarios(){
        return $this->hasMany('App\Models\Comentario','emprendedimiento_id');
    }

     // relacion uno a uno
     public function calificaciones(){
        return $this->hasMany('App\Models\calificacion','emprendedimiento_id');
    }
}
