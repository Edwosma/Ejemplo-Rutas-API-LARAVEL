<?php

namespace App\Http\Controllers;

use App\Models\calificacion;
use App\Http\Requests\StorecalificacionRequest;
use App\Http\Requests\UpdatecalificacionRequest;
/**
* @OA\Info(
*             title="Api Calificaciones", 
*             version="2.0",
*             description="Listado de las URI'S de la Api Calificaciones"
* )
*
* @OA\Server(url="http://127.0.0.1:8000")
*/



class CalificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Listado de todos los registros de calificaciones
     * @OA\Get (
     *     path="/api/calificaciones",
     *     tags={"Calificaciones"},
     *     @OA\Response(
     *         response=100,
     *         description="Succes",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="cliente_id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="emprendimiento_id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                      @OA\Property(
     *                         property="visualizacion_id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                      @OA\Property(
     *                         property="calificacion",
     *                         type="number",
     *                         example="4"
     *                     ),
     *                      @OA\Property(
     *                         property="estado",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2023-02-23T00:09:16.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2023-02-23T12:33:45.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return calificacion::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorecalificacionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorecalificacionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\calificacion  $calificacion
     * @return \Illuminate\Http\Response
     */
    public function show(calificacion $calificacion)
    {
        return $calificacion;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\calificacion  $calificacion
     * @return \Illuminate\Http\Response
     */
    public function edit(calificacion $calificacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatecalificacionRequest  $request
     * @param  \App\Models\calificacion  $calificacion
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatecalificacionRequest $request, calificacion $calificacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\calificacion  $calificacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(calificacion $calificacion)
    {
        //
    }
}
