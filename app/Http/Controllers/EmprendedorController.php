<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InformeGerencialMail;
use App\Mail\RegistroEmprendedorMail;
use App\Repositories\ResponseApiRepository;
use PDF;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Visualizacion;
use App\Models\Emprendimiento;

/**
* @OA\Infoo(
*             title="Api Emprendedor", 
*             version="1.0",
*             description="Listado de las URI'S de la Api Emprendedor"
* )
*
* @OA\Server(url="http://127.0.0.1:8000")
*/


class EmprendedorController extends Controller
{
  
    private $responseApiRepository;
    //
    public function __construct(ResponseApiRepository $responseApiRepository) {
              $this->responseApiRepository = $responseApiRepository;
    }
    public function registrarEmpredendor(Request $request){
        try {

            //valide el tipo de datos que pueden llegar con las restricciones establecidas
            $validatedData = $this->validate($request, [
                'nombreEmprendendor' => ['required', 'string'],
                'correoEmprendedor' => 'required|email',
                'passwordEmprendedor' => 'required',
                'fechaNacimientoEmprendedor' => ['required', 'date_format:Y-m-d'],
                'identificacionEmprendedor' => 'required|string|min:6|max:20',
                'tipoIdentificacionEmprendedor' => 'required|numeric|max:2',
                 //0=cedula.
                //1=rut
                //2=nit
            ]);


            //solicitamos la informacion que vamos a utilizar y la guardamos
            $name= $request->input('nombreEmprendendor');
            $email= $request->input('correoEmprendedor');
            $password= $request->input('passwordEmprendedor');
            $identification= $request->input('identificacionEmprendedor');
            $identification_type= $request->input('tipoIdentificacionEmprendedor');
            $birthdate= $request->input('fechaNacimientoEmprendedor');

            //busqueme si exite algun correo o cedula ya regustrada con esos datos
            $emprendedorValidacion = User::where('email',$email)->orwhere('identification',$identification)->first();
            //si si existe el dato envie el error
            if(isset($emprendedorValidacion->id)){
                $infoAdicional = array(
                    'mensajePersonalizado' => 'El emprendedor ya se encuentra registrado',
                );
                $respuesta = $this->responseApiRepository->error('410',$infoAdicional);
            }else{

                $fechaActual = now();
                $fechaNacimiento = \DateTime::createFromFormat('Y-m-d', $birthdate);
                $diff = $fechaActual->diff($fechaNacimiento);
                //valideme si el usuario es mayor de edad
                if ($diff->y >= 18) {
                    //si si guardelo
                    $emprendedor = new User();
                    $emprendedor->name= $name;
                    $emprendedor->email= $email;
                    $emprendedor->password= Hash::make($password);
                    $emprendedor->identification= $identification;
                    $emprendedor->birthdate= $birthdate;
                    $emprendedor->identification_type= $identification_type;
                    $emprendedor->estado= 0;
                     //0 = si esta inactivo
                    //1 = ssi esta activo
                    $emprendedor->estado_registro=0;//si esta gestionado o no
                     //0 = solicitud creada pero no gestionada
                    //1 = solicitud aprobada
                    //2 = solicitud rechazada
                    $emprendedor->save();

                    //y adicional envie un correo indicando el estado de la solicitud
                    $mailData = [
                        'title' => 'Nueva solicitud de registro',
                        'body' => 'El emprendedor '.$name. ' ha solicitado registrarse a la plataforma, con el siguiente correo: '.$email
                    ];

                    $correoAdmin =  env('CORREO_ADMIN', 'berakhaucatolica@gmail.com');

                    Mail::to($correoAdmin)->send(new RegistroEmprendedorMail($mailData));

                    $infoAdicional = array(
                        'mensajePersonalizado' => 'Se ha enviado la solicitud de registro',
                    );
                    $respuesta = $this->responseApiRepository->success($infoAdicional);

                }else{
                    // si no es mayor de edad envie error

                    $infoAdicional = array(
                        'mensajePersonalizado' => 'El emprendedor es menor de edad',
                    );
                    $respuesta = $this->responseApiRepository->error('410',$infoAdicional);
                }


            }


       //si no logra realizar la accion
        }catch (\Exception $e) {

            $infoAdicional = array(
                'mensaje' => $e->getMessage()
            );
            $respuesta = $this->responseApiRepository->error('400',$infoAdicional);
        }
        return response()->json($respuesta, 200);
    }

    public function gestionRegistroEmprendedor(Request $request){

        try {
            //valide la informacion recibida
            $validatedData = $this->validate($request, [
                'identificacionEmprendedor' => 'required|string|min:6|max:20',
                'correoEmprendedor' => 'required|email',
                'estadoSolicitud' => 'required|numeric|max:2',
                //0 = solicitud creada pero no gestionada
                //1 = solicitud aprobada
                //2 = solicitud rechazada
            ]);
            //guardamos los datos
            $email= $request->input('correoEmprendedor');
            $identification= $request->input('identificacionEmprendedor');
            $estadoRegistro= $request->input('estadoSolicitud');

            //busqueme si exite algun correo o cedula ya regustrada con esos datos
            $emprendedor = User::where('email',$email)->where('identification',$identification)->first();
            //validamos si existe alguna solicitud o registro
            if(isset($emprendedor->id)){
                //si existe
                switch($estadoRegistro){

                    //si el administrador envia datos de apobacion
                    case 1:
                        $emprendedor->estado = 1;
                        $emprendedor->estado_registro=1;
                        $emprendedor->save();

                        $mailData = [
                            'title' => 'Bienvenid@ a Berakha',
                            'body' => $emprendedor->name. ' te damos la bienvenida a la primera plataforma de empredendores de Colombia. Ya puedes ingresar a la plataforma y registrar tus emprendimientos'
                        ];

                        Mail::to($email)->send(new RegistroEmprendedorMail($mailData));

                        $infoAdicional = array(
                            'mensajePersonalizado' => 'Se ha habilitado el emprendedor',
                        );
                        $respuesta = $this->responseApiRepository->success($infoAdicional);

                        break;
                     //si el administrador envia datos de rechazo
                    case 2:
                        $emprendedor->estado = 0;
                        $emprendedor->estado_registro=2;
                        $emprendedor->save();

                        $mailData = [
                            'title' => 'Lo sentimos',
                            'body' => $emprendedor->name. ' lamentamos decirte que en estos momentos no podemos aprobar tu solicitud'
                        ];

                        Mail::to($email)->send(new RegistroEmprendedorMail($mailData));

                        $infoAdicional = array(
                            'mensajePersonalizado' => 'Se rechazo la solicitud del emprendedor',
                        );
                        $respuesta = $this->responseApiRepository->success($infoAdicional);

                        break;
                         //si el administrador envia un estado diferente a 1 o 2
                    default:

                        $infoAdicional = array(
                            'mensajePersonalizado' => 'El estado no es valido',
                        );
                        $respuesta = $this->responseApiRepository->error('410',$infoAdicional);

                        break;
                }
            }else{

                //si el correo o identificacion no tienen un registro o solicitud
                    $infoAdicional = array(
                    'mensajePersonalizado' => 'El emprendedor no se encuentra registrado',
                );
                $respuesta = $this->responseApiRepository->error('410',$infoAdicional);

            }

        }catch (\Exception $e) {

            $infoAdicional = array(
                'mensaje' => $e->getMessage()
            );
            $respuesta = $this->responseApiRepository->error('400',$infoAdicional);
        }
        return response()->json($respuesta, 200);


    }

    public function consultarEmprendoresEstadoSolicitud($estadoSolicitud = null){
        try {
            if ($estadoSolicitud !== null) {
                if (is_numeric($estadoSolicitud)) {

                    $emprendedor = null;
                    switch($estadoSolicitud){
                        case 0:
                            $mensaje = "Emprendedores pendientes por validar";
                            $emprendedor = User::where('estado_registro',$estadoSolicitud)->get();
                            break;
                        case 1:
                            $mensaje = "Emprendedores aprobados";
                            $emprendedor = User::where('estado_registro',$estadoSolicitud)->get();
                            break;
                        case 2:
                            $mensaje = "Emprendedores rechazados";
                            $emprendedor = User::where('estado_registro',$estadoSolicitud)->get();
                        break;
                        case 3:
                            $mensaje = "Lista total de emprendedores";
                            $emprendedor = User::all();
                        break;
                        default:
                            $mensaje = "Ingrese un estado valido";
                            $respuesta = "error";
                            $codigo = "200";
                        break;
                    }
                    if ($mensaje=="Ingrese un estado valido"){
                        $infoAdicional = array(
                            'mensajePersonalizado' => 'Ingrese un estado valido',
                        );
                        $respuesta = $this->responseApiRepository->error('410',$infoAdicional);


                    }
                    else{

                    $infoAdicional = array(
                        'mensaje' =>  $mensaje,
                        'emprendedores' => $emprendedor,
                    );

                    $respuesta = $this->responseApiRepository->success($infoAdicional);
                    }

                }else{

                    $infoAdicional = array(
                        'mensajePersonalizado' => 'Ingrese un estado numerico',
                    );
                    $respuesta = $this->responseApiRepository->error('410',$infoAdicional);

                }

            }else{

                $infoAdicional = array(
                   'mensajePersonalizado' => 'Ingrese un estado',
                );
                $respuesta = $this->responseApiRepository->error('410',$infoAdicional);

            }

        }
        catch (\Exception $e) {

            $infoAdicional = array(
                'mensaje' => $e->getMessage()
            );
            $respuesta = $this->responseApiRepository->error('400',$infoAdicional);
        }
        return response()->json($respuesta, 200);
    }
    private function factorial($n) 
    {
        if ($n <= 1 ) {
            return 1;
         }
         $factorial =1;
            for ($i=2;$i<=$n;$i++){
                $factorial *=$i;
            }
            return $factorial;
        
    }
    

    
    public function calcularPrediccionPoisson($lambda, $conteoVisualizaciones) {
        return ((($conteoVisualizaciones/2)*($conteoVisualizaciones/2))/(2))*((exp(-($conteoVisualizaciones/2))));
    }

    
   

    public function informeGerencial(){
        //$emprendedores = User::where('estadoRegistro',1)->get();
        //foreach($emprendedores as $emprendedor){
            //
        //}
        try {

            $emprendedores = User::where('estado_registro',1)->get();

            //$emprendedor = User::where('estado_registro',1)->first();
            foreach($emprendedores as $emprendedor){
                $datosEmprendimientos = User::with(['emprendimientos.visualizaciones', 'emprendimientos.comentarios', 'emprendimientos.calificaciones'])->find($emprendedor->id);

                    if (!$emprendedor) {
                        return response()->json(['message' => 'Emprendedor no encontrado'], 404);
                    }

                    $resumenEmpredimientos = $emprendedor->emprendimientos->map(function ($emprendimiento) {
                        $conteoVisualizaciones = $emprendimiento->visualizaciones->count();
                        $tiempoObservacion = 2; // Coloca el tiempo de observación en horas aquí
                        $lambda=($conteoVisualizaciones/2);
                        //$prediccion = $this->calcularPrediccionPoisson($conteoVisualizaciones, $tiempoObservacion);
                        $prediccion= ((($conteoVisualizaciones/2)*($conteoVisualizaciones/2))/(2))*((exp(-($conteoVisualizaciones/2))));
                        return [
                            'nombre' => $emprendimiento->nombre_emprendimiento,
                            'conteoVisualizaciones' => $conteoVisualizaciones,
                            'prediccionVisualizaciones' => number_format($prediccion * 100, 2),
                            'conteoComentarios' => $emprendimiento->comentarios->count(),
                            'conteoCalificaciones' => $emprendimiento->calificaciones->count(),
                        ];
                    });


                $mailData = [
                    'title' => 'Informe',
                    'body' => $emprendedor->name. ' adjunto encontrará el informe gerencial'
                ];


                Mail::to($emprendedor->email)->send(new InformeGerencialMail($mailData,$datosEmprendimientos, $resumenEmpredimientos));
            }

            $infoAdicional = array(
                'cantEmprendedores' => count($emprendedores)
            );

            $respuesta = $this->responseApiRepository->success($infoAdicional);

        } catch (\Exception $e) {

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
     *     path="/api/emprendedores",
     *     tags={"Emprendedores"},
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
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Felix"
     *                     ),
     *                      @OA\Property(
     *                         property="email",
     *                         type="email",
     *                         example="aleacosta@.com"
     *                     ),
     *                      @OA\Property(
     *                         property="identification",
     *                         type="string",
     *                         example="123456"                                               
     *                     ),
     *                      @OA\Property(
     *                         property="identification_type",
     *                         type="number",
     *                         example="0"
     *                     ),
     *                      @OA\Property(
     *                         property="estado",
     *                         type="number",
     *                         example="1"
     *                     ),    
     *                     @OA\Property(
     *                         property="password",
     *                         type="string",
     *                         example="123456"                                               
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
        return User::all();
    }
}