<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visualizacion;
use App\Models\Cliente;
use App\Models\Emprendimiento;
use App\Http\Requests\StoreVisualizacionRequest;
use App\Http\Requests\UpdateVisualizacionRequest;
use App\Repositories\ResponseApiRepository;

class VisualizacionController extends Controller
{
    private $responseApiRepository;
    //
    public function __construct(ResponseApiRepository $responseApiRepository) {
              $this->responseApiRepository = $responseApiRepository;}
    public function visualizarEmprendimiento(Request $request){
        try {
            $validatedData = $this->validate($request, [
                'clienteId' => 'required|numeric',
                'emprendimientoId' => 'required|numeric'
            ]);

            $cliente_id= $request->input('clienteId');
            $emprendedimiento_id= $request->input('emprendimientoId');

            $clienteValidacion = Cliente::where('id', $cliente_id)->first();
            if(isset($clienteValidacion ->id)){
                $empredimientoValidacion = Emprendimiento::where('id', $emprendedimiento_id)->first();
                if(isset($empredimientoValidacion->id)){
                    $visualizacion = Visualizacion::where('cliente_id',$cliente_id)->where('emprendedimiento_id',$emprendedimiento_id)->first();
                    if(isset($visualizacion->id)){
                        $contador = $visualizacion->conteo;
                        $contador++;
                        $visualizacion->conteo = $contador;
                        $visualizacion->save();
                    }else{
                        $visualizacion = new Visualizacion();
                        $visualizacion->cliente_id = $cliente_id;
                        $visualizacion->emprendedimiento_id = $emprendedimiento_id;
                        $visualizacion->conteo = 1;
                        $visualizacion->save();
                    }
                    
                    $infoAdicional = array(
                        'mensajePersonalizado' => 'Se registro la visualización',                        
                    );
                    $respuesta = $this->responseApiRepository->success($infoAdicional);

                }else{
                    
                    $infoAdicional = array(
                        'mensajePersonalizado' => 'No se encuentra un Emprendimiento con el id ingresado',                                       
                    );
                    $respuesta = $this->responseApiRepository->error('440',$infoAdicional);
                }

            }else{
                
                $infoAdicional = array(
                    'mensajePersonalizado' => 'No se encuentra un Cliente con el id ingresado',                                       
                );
                $respuesta = $this->responseApiRepository->error('440',$infoAdicional);
            }



        }catch (\Exception $e) {
            $infoAdicional = array(
                'mensaje' => $e->getMessage()                                      
            );
            $respuesta = $this->responseApiRepository->error('400',$infoAdicional);
        }
        return response()->json($respuesta, 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreVisualizacionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVisualizacionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Visualizacion  $visualizacion
     * @return \Illuminate\Http\Response
     */
    public function show(Visualizacion $visualizacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Visualizacion  $visualizacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Visualizacion $visualizacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVisualizacionRequest  $request
     * @param  \App\Models\Visualizacion  $visualizacion
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVisualizacionRequest $request, Visualizacion $visualizacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Visualizacion  $visualizacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Visualizacion $visualizacion)
    {
        //
    }
}
