<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/registrar-emprendedor', array(
    'as' => 'registrarEmpredendor',
    'uses' => 'EmprendedorController@registrarEmpredendor'
));

Route::post('/gestionar-registro-emprendedor', array(
    'as' => 'gestionRegistroEmprendedor',
    'uses' => 'EmprendedorController@gestionRegistroEmprendedor'
));

Route::get('/emprendedores-estado-solicitud/{estadoSolicitud}', array(
    'as' => 'consultarEmprendoresEstadoSolicitud',
    'uses' => 'EmprendedorController@consultarEmprendoresEstadoSolicitud'
));

Route::post('/crear-emprendimiento', array(
    'as' => 'crearEmprendimiento',
    'middleware' => ['auth:sanctum'],
    'uses' => 'EmprendimientoController@crearEmprendimiento'
));
Route::get('/consultar-empredimientos-estado/{estado}', array(
    'as' => 'consultarEmprendimientoEstado',
    'middleware' => ['auth:sanctum'],
    'uses' => 'EmprendimientoController@consultarEmprendimientoEstado'
));
Route::post('/registrar-cliente', array(
    'as' => 'registrarCliente',
    'uses' => 'ClienteController@registrarCliente'
));

Route::post('/gestionar-registro-cliente', array(
    'as' => 'gestionRegistroCliente',
    'uses' => 'ClienteController@gestionRegistroCliente'
));

Route::post('/visualizar-emprendimiento', array(
    'as' => 'visualizarEmprendimiento',
    'middleware' => ['auth:sanctum'],
    'uses' => 'VisualizacionController@visualizarEmprendimiento'
));

Route::post('/comentar-emprendimiento', array(
    'as' => 'comentarEmprendimiento',
    'middleware' => ['auth:sanctum'],
    'uses' => 'EmprendimientoController@comentarEmprendimiento'
));

Route::post('/calificar-emprendimiento', array(
    'as' => 'calificarEmprendimiento',
    'middleware' => ['auth:sanctum'],
    'uses' => 'EmprendimientoController@calificarEmprendimiento'
));

Route::get('/calificaciones', array(
    'as' => 'index',
    'uses' => 'CalificacionController@index'
));

Route::get('/informe-gerencial', array(
    'as' => 'informeGerencial',
   
    'uses' => 'EmprendedorController@informeGerencial'
));

Route::post('/register/user', 'API\AuthController@registerUser');
Route::post('/login/user', 'API\AuthController@loginUser');
Route::post('/register/cliente', 'API\AuthController@registerCliente');
Route::post('/login/cliente', 'API\AuthController@loginCliente');
//faltan rutas
