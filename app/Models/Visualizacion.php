<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visualizacion extends Model
{
    use HasFactory;
    protected $table = 'visualizaciones';


    public function emprendimiento()
    {
        return $this->belongsTo('App\Models\emprendimiento','emprendedimiento_id');
    }

    public function cliente()
    {
        return $this->belongsTo('App\Models\Cliente', 'cliente_id');
    }
}
