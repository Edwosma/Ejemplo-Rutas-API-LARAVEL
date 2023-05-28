<?php

namespace App\Repositories;

class ResponseApiService implements ResponseApiRepository {
    private $codigosError = [
        '400' => 'Error generico de ingreso de Informacion',
        '410' => 'Error generico de Emprendedor',
        '420' => 'Error generico de Cliente',
        '430' => 'Error generico de Emprendimiento',
        '440' => 'Error generico de Visualizacion',
        '450' => 'Error generico de Comentario',
        '460' => 'Error generico de Calificacion',
       

        // Agregar más códigos de error y sus mensajes correspondientes
    ];

    public function success($data = null): array {
        return [
            'respuesta' => 'Success',
            'code' => '100',
            'mensaje'=> 'Operación exitosa',
            'datos' => $data
        ];
    }

    public function error(string $codigo, $data = null): array {
        return [
            'respuesta' => 'Error',
            'codigo' => $codigo,
            'mensaje' => $this->getErrorMessage($codigo),
            'datos' => $data
        ];
    }
    public function getErrorMessage(string $codigo): string {
        if(isset($this->codigosError[$codigo])){
           $mensaje = $this->codigosError[$codigo];
        }else{
            $mensaje = 'Error no registrado';
        }
        return $mensaje;
    }


}
