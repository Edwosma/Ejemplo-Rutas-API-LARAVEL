<?php

namespace App\Repositories;

use App\Models\calificacion;

class CalificacionService implements CalificacionRepository {
    public function insertarCalificacion(array $data): calificacion {
        $calificacion = new calificacion($data);
        $calificacion->save();
        return $calificacion;
    }

    public function actualizarCalificacion(int $id, array $data): bool {
        $calificacion = calificacion::find($id);
        if (!$calificacion) {
            return false;
        }
        $calificacion->fill($data);
        $calificacion->save();
        return true;
    }

    public function prueba ():bool{
        return true;
    }

    public function verificacionCalificacion(int $clienteId, int $emprendimientoId): ?calificacion {
        return calificacion::where('cliente_id', $clienteId)->where('emprendedimiento_id', $emprendimientoId)->first();
    }
}
