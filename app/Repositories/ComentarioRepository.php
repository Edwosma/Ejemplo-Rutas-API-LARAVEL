<?php
namespace App\Repositories;

use App\Models\Comentario;

interface ComentarioRepository {
    public function insertarComentario(array $data): Comentario;
    public function actualizarComentario(int $id, array $data): bool;
    public function prueba():bool;
    public function verificacionComentario(int $clienteId, int $emprendimientoId): ?Comentario;
}
