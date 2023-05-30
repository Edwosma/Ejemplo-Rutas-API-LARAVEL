<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;
use App\Repositories\ResponseApiRepository;
use App\Models\Cliente;

class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;

    private $responseApiRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseApiRepository = $this->app->make(ResponseApiRepository::class);
    }

    public function testRegistrarCliente()
    {
        // Crear una instancia del controlador
        $controller = new ClienteController($this->responseApiRepository);

        // Crear una instancia de la solicitud con los datos necesarios
        $request = Request::create('/registrar-cliente', 'POST', [
            'nombre' => 'John Doe',
            'correo' => 'johndoe@example.com',
            'clave' => 'password',
            'identificacion' => '123456789',
            'fechaNacimiento' => '1990-01-01',
        ]);

        // Ejecutar la función del controlador
        $response = $controller->registrarCliente($request);

        // Verificar el estado de la respuesta
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar el contenido de la respuesta
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Success', $responseData['respuesta']);
        $this->assertEquals('100', $responseData['code']);
        $this->assertEquals('Operación exitosa', $responseData['mensaje']);
        $this->assertNotNull($responseData['datos']);

        // Verificar si se creó un cliente en la base de datos
        $cliente = Cliente::where('correo', 'johndoe@example.com')->first();
        $this->assertNotNull($cliente);
        $this->assertEquals('John Doe', $cliente->nombre);
        // Verificar más atributos del cliente si es necesario
    }
}
