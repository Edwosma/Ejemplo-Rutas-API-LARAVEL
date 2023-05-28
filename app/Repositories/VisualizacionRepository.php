<?php
namespace App\Repositories;

use App\Models\Visualizacion;

interface VisualizacionRepository {
    public function verificacionVisualizacion(int $clienteId, int $emprendimientoId): ?Visualizacion;
}
