<?php
namespace App\Repositories;
//implementacion de SOLID
//con Inyeccion, responsabilidad unica, y inversion y listo
interface ResponseApiRepository {
    public function success($data = null): array;
    public function error(string $codigo, $data = null): array;
    public function getErrorMessage(string $codigo): string;//en un futuro consulta de errores creados
}