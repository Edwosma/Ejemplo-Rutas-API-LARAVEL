<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emprendimiento;
use App\Models\Cliente;
use App\Models\TiposEmprendimiento;
use App\Repositories\ComentarioRepository;
use App\Repositories\CalificacionRepository;
use App\Repositories\VisualizacionRepository;
use App\Repositories\ResponseApiRepository;
use App\Models\User;
/**
* @OA\Infoooo(
*             title="Api Emprendimientos", 
*             version="2.0",
*             description="Listado de las URI'S de la Api Emprendimientos"
* )
*
* @OA\Server(url="http://127.0.0.1:8000")
*/
class EmprendimientoController extends Controller
{
     
   
    private $comentarioRepository;
    private $calificacionRepository;
    private $visualizacionRepository;
    private $responseApiRepository;
    public function __construct(CalificacionRepository $calificacionRepository,ComentarioRepository $comentarioRepository,VisualizacionRepository $visualizacionRepository,ResponseApiRepository $responseApiRepository) {
        $this->comentarioRepository = $comentarioRepository;
        $this->calificacionRepository = $calificacionRepository;
        $this->visualizacionRepository = $visualizacionRepository;
        $this->responseApiRepository = $responseApiRepository;
    
    }

    public function crearEmprendimiento (Request $request){
        try {
            //valideme esta informacion
            $validatedData = $this->validate($request, [
                'idEmprendor' => 'required',
                'tipoEmprendimiento' => 'required|numeric|max:255',
                'nombreEmprendimiento' => ['required', 'string'],
                'descripcionEmprendimiento' => ['required', 'string'],
            ]);

            //guardeme esa infoormacion q le pido al emprendedor
            $emprendedor_id= $request->input('idEmprendor');
            $tipo_emprendimiento= $request->input('tipoEmprendimiento');
            $nombre_emprendimiento= $request->input('nombreEmprendimiento');
            $descripcion= $request->input('descripcionEmprendimiento');

            $emprendedor = User::where('id',$emprendedor_id)->where('estado',1)->first();
            if(isset($emprendedor->id)){
                $tipoEmprendimiento=TiposEmprendimiento::where('id',$tipo_emprendimiento)->first();
                if(isset($tipoEmprendimiento->id)){
                    $emprendimiento = emprendimiento::where('nombre_emprendimiento',$nombre_emprendimiento)
                        ->where('tipo_emprendimiento',$tipo_emprendimiento)
                        ->where('emprendedor_id',$emprendedor_id)
                        ->first();
                    if(!isset($emprendimiento->id)){
                        $emprendimiento = new emprendimiento();
                        $emprendimiento->nombre_emprendimiento=$nombre_emprendimiento;
                        $emprendimiento->descripcion=$descripcion;
                        $emprendimiento->estado=1;
                        $emprendimiento->tipo_emprendimiento=$tipo_emprendimiento;
                        $emprendimiento->emprendedor_id=$emprendedor_id;
                        $emprendimiento->save();

                        
                        $infoAdicional = array(
                            'mensajePersonalizado' => 'Emprendimiento creado con exito',                        
                        );
                        $respuesta = $this->responseApiRepository->success($infoAdicional);
    

                    }else{
                
                        $infoAdicional = array(
                            'mensajePersonalizado' => 'Ya existe un emprendimiento creado',                                       
                        );
                        $respuesta = $this->responseApiRepository->error('430',$infoAdicional);
                    }
                }else{
                    
                    $infoAdicional = array(
                        'mensajePersonalizado' => 'No existe el tipo de emprendimiento suministrado',                                       
                    );
                    $respuesta = $this->responseApiRepository->error('430',$infoAdicional);
                }
            }else{               
                
                $infoAdicional = array(
                    'mensajePersonalizado' => 'No existe un emprendedor con el id suministrado activo',                                       
                );
                $respuesta = $this->responseApiRepository->error('430',$infoAdicional);
            }
        }catch (\Exception $e) {
            $infoAdicional = array(
                'mensaje' => $e->getMessage()                                      
            );
            $respuesta = $this->responseApiRepository->error('400',$infoAdicional);
        }
        return response()->json($respuesta, 200);

    }

    public function consultarEmprendimientoEstado($estado = null){
        try {
            $respuesta = 'Error generico de Emprendimiento';
            $codigo = '430';
            $mensajeRespuesta = 'ok';
            $emprendedimientos = null;
            if ($estado !== null) {
                if (is_numeric($estado)) {
                    switch($estado){
                        case 0:
                            $respuesta = "Operación exitosa";
                            $codigo = "100";
                            $mensajeRespuesta = "Emprendimientos inactivos";
                            $emprendedimientos = Emprendimiento::where('estado',0)->get();
                            break;
                        case 1:
                            $respuesta = "Operación exitosa";
                            $codigo = "100";
                            $mensajeRespuesta = "Emprendimientos activos";
                            $emprendedimientos = Emprendimiento::where('estado',1)->get();
                            break;
                        default:
                            $mensajeRespuesta = "Ingrese un estado valido";
                            $respuesta = "Error generico de Emprendimiento";
                            $codigo = "430";
                            break;
                    }
                }else{
                    $mensajeRespuesta = 'Ingrese un estado numerico';
                }
            }else{
                $mensajeRespuesta = 'Ingrese un estado';
            }
            $respuesta = array(
                'respuesta' => $respuesta,
                'codigo' =>  $codigo,
                'mensaje' => $mensajeRespuesta,
                'emprendedimientos' => $emprendedimientos
            );
        }catch (\Exception $e) {
            $infoAdicional = array(
                'mensaje' => $e->getMessage()                                      
            );
            $respuesta = $this->responseApiRepository->error('400',$infoAdicional);
        }
        return response()->json($respuesta, 200);
    }


    public function comentarEmprendimiento(Request $request){
        try {
            $validatedData = $this->validate($request, [
                'clienteId' => 'required|numeric',
                'emprendimientoId' => 'required|numeric',
                'comentario' => 'required'
            ]);
            $cliente_id= $request->input('clienteId');
            $emprendedimiento_id= $request->input('emprendimientoId');
            $comentario= $request->input('comentario');

            $clienteValidacion = Cliente::where('id', $cliente_id)->first();
            if(isset($clienteValidacion ->id)){
                $empredimientoValidacion = Emprendimiento::where('id', $emprendedimiento_id)->first();
                if(isset($empredimientoValidacion->id)){

                    $visualizacionEmprendimiento = $this->visualizacionRepository->verificacionVisualizacion($cliente_id,$emprendedimiento_id);
                    if($visualizacionEmprendimiento){
                        $comentarioValidacion = $this->comentarioRepository->verificacionComentario($cliente_id,$emprendedimiento_id);
                        $data = [
                            'cliente_id' => $cliente_id,
                            'emprendedimiento_id' => $emprendedimiento_id,
                            'visualizacion_id' => $visualizacionEmprendimiento->id,
                            'comentario' => $comentario,
                            'estado'=> 1
                        ];
                        if ($comentarioValidacion) {
                            $bandActualizacion = $this->comentarioRepository->actualizarComentario($comentarioValidacion->id, $data);
                            if($bandActualizacion){
                                $infoAdicional = array(
                                    'mensajePersonalizado' => 'Comentario actualizado'
                                );
                                $respuesta = $this->responseApiRepository->success($infoAdicional);
                            }else{
                                $respuesta = array(
                                    'respuesta' => 'ok',
                                    'codigo' => '100',
                                    'mensaje' => 'Comentario No actualizado'
                                );
                                
                            }

                        }else{
                            $comentarioInsertado = $this->comentarioRepository->insertarComentario($data);
                            if($comentarioInsertado){
                                $infoAdicional = array(
                                    'mensajePersonalizado' => 'Comentario registrado',
                                    'comentario' => $comentarioInsertado,
                                );
                                $respuesta = $this->responseApiRepository->success($infoAdicional);

                            }else{
                                
                                $infoAdicional = array(
                                    'mensajePersonalizado' => 'El Comentario no se pudo registrar',
                                );
                                $respuesta = $this->responseApiRepository->error('450',$infoAdicional);
                            }
                        }
                    }else{
                     
                        $infoAdicional = array(
                            'mensajePersonalizado' => 'No ha realizado una visualizacion',
                        );
                        $respuesta = $this->responseApiRepository->error('450',$infoAdicional);
                    }
                }else{
                 
                    $infoAdicional = array(
                        'mensajePersonalizado' => 'No se encuentra el emprendimiento',
                    );
                    $respuesta = $this->responseApiRepository->error('450',$infoAdicional);
                }
            }else{
                $infoAdicional = array(
                    'mensajePersonalizado' => 'No se encontro un cliente con el id suministrado',
                );
                $respuesta = $this->responseApiRepository->error('450',$infoAdicional);
            }
        }catch (\Exception $e) {
            $infoAdicional = array(
                'mensaje' => $e->getMessage()                                      
            );
            $respuesta = $this->responseApiRepository->error('400',$infoAdicional);
        }
        return response()->json($respuesta, 200);
    }

    public function calificarEmprendimiento(Request $request){
        try {
            $validatedData = $this->validate($request, [
                'clienteId' => 'required|numeric',
                'emprendimientoId' => 'required|numeric',
                
                'calificacion' => ['required', 'numeric', 'in:0,1,2,3,4,5']
            ]);
            $cliente_id= $request->input('clienteId');
            $emprendedimiento_id= $request->input('emprendimientoId');
            $calificacion= $request->input('calificacion');

            //busqueme si exite algun cliente ya registrada con esos datos
            $clienteValidacion = Cliente::where('id', $cliente_id)->first();
            if(isset($clienteValidacion ->id)){
                //busqueme si exite algun emprendimiento ya registrada con esos datos
                $empredimientoValidacion = Emprendimiento::where('id', $emprendedimiento_id)->first();
                if(isset($empredimientoValidacion->id)){

                    //por solid inversion de dependencias valide si exite alguna valide si exite algun una visualizacion ya registrada con esos datos

                    $visualizacionEmprendimiento = $this->visualizacionRepository->verificacionVisualizacion($cliente_id,$emprendedimiento_id);
                    if($visualizacionEmprendimiento){

                        //por solid inversion de dependencias valide si exite alguna calificacion ya registrada con esos datos
                        $calificacionValidacion = $this->calificacionRepository->verificacionCalificacion($cliente_id,$emprendedimiento_id);
                        $data = [
                            'cliente_id' => $cliente_id,
                            'emprendedimiento_id' => $emprendedimiento_id,
                            'visualizacion_id' => $visualizacionEmprendimiento->id,
                            'calificacion' => $calificacion,
                            'estado'=> 1
                        ];
                        // en el if se utiliza el $ indicando que si calificacionValidacion tiene algo dentro se toma como verdadero
                        if ($calificacionValidacion) {
                            $bandActualizacion = $this->calificacionRepository->actualizarCalificacion($calificacionValidacion->id, $data);
                            if($bandActualizacion){
                                $infoAdicional = array(
                                    'mensajePersonalizado' => 'Calificacion actualizado'
                                );
                                $respuesta = $this->responseApiRepository->success($infoAdicional);
                            }else{
                                $respuesta = array(
                                    'respuesta' => 'ok',
                                    'codigo' => '100',
                                    'mensaje' => 'Calificacion No actualizado'
                                );
                                
                            }

                        }else{
                            //si no existia una calificacion registrada creela
                            $calificacionInsertado = $this->calificacionRepository->insertarCalificacion($data);
                            if($calificacionInsertado){
                                $infoAdicional = array(
                                    'mensajePersonalizado' => 'Calificacion registrado',
                                    'calificacion' => $calificacionInsertado,
                                );
                                $respuesta = $this->responseApiRepository->success($infoAdicional);

                            }else{
                                
                                $infoAdicional = array(
                                    'mensajePersonalizado' => 'El Calificacion no se pudo registrar',
                                );
                                $respuesta = $this->responseApiRepository->error('460',$infoAdicional);
                            }
                        }
                    }else{
                     
                        $infoAdicional = array(
                            'mensajePersonalizado' => 'No ha realizado una visualizacion',
                        );
                        $respuesta = $this->responseApiRepository->error('460',$infoAdicional);
                    }
                }else{
                 
                    $infoAdicional = array(
                        'mensajePersonalizado' => 'No se encuentra el emprendimiento',
                    );
                    $respuesta = $this->responseApiRepository->error('460',$infoAdicional);
                }
            }else{
                $infoAdicional = array(
                    'mensajePersonalizado' => 'No se encontro un cliente con el id suministrado',
                );
                $respuesta = $this->responseApiRepository->error('460',$infoAdicional);
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
        /**
         * Listado de todos los registros de calificaciones
         * @OA\Get (
         *     path="/api/emprendimientos",
         *     tags={"Emprendimientos"},
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
         *                         property="idEmprendor",
         *                         type="number",
         *                         example="1"
         *                     ),
         *                      @OA\Property(
         *                         property="nombre_emprendimiento",
     *                         type="string",
     *                         example="Terramoda"
         *                     ),
         *                      @OA\Property(
         *                         property="descripcion",
     *                         type="string",
     *                         example="moda y mucho mas"
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
            return Emprendimiento::all();
        }
    
}

