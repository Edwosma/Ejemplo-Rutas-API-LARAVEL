<?php

namespace Tests\Unit;

use App\Http\Controllers\EmprendedorController;
use App\Repositories\ResponseApiRepository;
use PHPUnit\Framework\TestCase;

class CalcularPrediccionPoissonTest extends TestCase
{
    public function testCalcularPrediccionPoisson()
    {
        // Arrange
        $responseApiRepository = $this->createMock(ResponseApiRepository::class);
        $emprendedorController = new EmprendedorController($responseApiRepository);

        // Act
        $lambda = 2;
        $conteoVisualizaciones = 5;
        $prediccion = $emprendedorController->calcularPrediccionPoisson($lambda, $conteoVisualizaciones);

        // Assert
        $this->assertEquals(0.25651562069968376, $prediccion);
    }
}



