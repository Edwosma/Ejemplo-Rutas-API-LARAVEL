<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;





use App\Repositories\ResponseApiRepository;
use App\Models\User;


class EmprendedorControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var ResponseApiRepository
     */
    private $responseApiRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseApiRepository = $this->app->make(ResponseApiRepository::class);
    }

  /**
 * Test para verificar el registro exitoso de un emprendedor.
 *
 * @return void
 */
public function testRegistrarEmpredendorExitoso()
{
    $data = [
        'nombreEmprendendor' => 'John Doe',
        'correoEmprendedor' => 'johndoe@example.com',
        'passwordEmprendedor' => 'password',
        'fechaNacimientoEmprendedor' => '1990-01-01',
        'identificacionEmprendedor' => '123456789',
        'tipoIdentificacionEmprendedor' => 0,
    ];

    $response = $this->post(route('registrarEmpredendor'), $data);

    $response->assertStatus(200)
        ->assertJson([
            'respuesta' => 'Success',
            'code' => '100',
        ]);

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
    ]);
}





}