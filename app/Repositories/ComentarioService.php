<?php

namespace App\Repositories;

use App\Models\Comentario;

class ComentarioService implements ComentarioRepository {
    public function insertarComentario(array $data): Comentario {
        $comentario = new Comentario($data);
        $comentario->save();
        return $comentario;
    }

    public function actualizarComentario(int $id, array $data): bool {
        $comentario = Comentario::find($id);
        if (!$comentario) {
            return false;
        }
        $comentario->fill($data);
        $comentario->save();
        return true;
    }

    public function prueba ():bool{
        return true;
    }

    public function verificacionComentario(int $clienteId, int $emprendimientoId): ?Comentario {
        return Comentario::where('cliente_id', $clienteId)->where('emprendedimiento_id', $emprendimientoId)->first();
    }
}
