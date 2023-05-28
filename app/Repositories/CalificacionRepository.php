<?php
namespace App\Repositories;

use App\Models\calificacion;

interface CalificacionRepository {
    public function insertarCalificacion(array $data): calificacion;
    public function actualizarCalificacion(int $id, array $data): bool;
    public function prueba():bool;
    public function verificacionCalificacion(int $clienteId, int $emprendimientoId): ?calificacion;
}


