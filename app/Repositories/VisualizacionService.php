<?php

namespace App\Repositories;

use App\Models\Visualizacion;

class VisualizacionService implements VisualizacionRepository {
    public function verificacionVisualizacion(int $clienteId, int $emprendimientoId): ?Visualizacion {
        return Visualizacion::where('cliente_id', $clienteId)->where('emprendedimiento_id', $emprendimientoId)->first();
    }
}
