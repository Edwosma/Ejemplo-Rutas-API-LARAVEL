<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'emprendedimiento_id',
        'visualizacion_id',
        'comentario',
        'estado',
        'updated_at'
    ];

    public function emprendimiento()
    {
        return $this->belongsTo('App\Models\emprendimiento','emprendedimiento_id');
    }

    public function cliente()
    {
        return $this->belongsTo('App\Models\Cliente', 'cliente_id');
    }
}
