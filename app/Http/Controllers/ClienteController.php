<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Repositories\ResponseApiRepository;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistroClienteMail;
use Illuminate\Support\Facades\Hash;
/**
* @OA\Infoor(
*             title="Api Cliente", 
*             version="1.0",
*             description="Listado de las URI'S de la Api Clientes"
* )
*
* @OA\Server(url="http://127.0.0.1:8000")
*/
class ClienteController extends Controller
{
    private $responseApiRepository;
    //
    public function __construct(ResponseApiRepository $responseApiRepository) {
              $this->responseApiRepository = $responseApiRepository;}

     //
    public function registrarCliente(Request $request){
        try {

            $validatedData = $this->validate($request, [
                'nombre' => ['required', 'string'],
                'correo' => 'required|email',
                'clave' => 'required',
                'fechaNacimiento' => ['required', 'date_format:Y-m-d'],
                'identificacion' => 'required|string|min:6|max:20',
            ]);


            $nombre= $request->input('nombre');
            $correo= $request->input('correo');
            $clave= $request->input('clave');
            $identificacion= $request->input('identificacion');
            $fecha_nacimiento= $request->input('fechaNacimiento');

            $clienteValidacion = Cliente::where('correo',$correo)->orwhere('identificacion',$identificacion)->first();


                if(isset($clienteValidacion->id)){
                    $infoAdicional = array(
                        'mensajePersonalizado' => 'El cliente ya se encuentra registrado',
                    );
                    $respuesta = $this->responseApiRepository->error('420',$infoAdicional);
            }else{
                $fechaActual = now();
                $fechaNacimiento = \DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
                $diff = $fechaActual->diff($fechaNacimiento);
                if ($diff->y >= 18) {
                    $cliente = new Cliente();
                    $cliente->nombre = $nombre;
                    $cliente->correo = $correo;
                    $cliente->clave = Hash::make($clave);
                    $cliente->identificacion = $identificacion;
                    $cliente->fecha_nacimiento = $fecha_nacimiento;
                    $cliente->estado = 0;
                    $cliente->estado_registro = 0;
                    $cliente->save();

                    $mailData = [
                        'title' => 'Nueva solicitud de registro de cliente',
                        'body' => 'El cliente '.$nombre. ' ha solicitado registrarse a la plataforma, con el siguiente correo: '.$correo
                    ];

                    $correoAdmin =  env('CORREO_ADMIN', 'berakhaucatolica@gmail.com');

                    Mail::to($correoAdmin)->send(new RegistroClienteMail($mailData));

                    $infoAdicional = array(
                        'mensajePersonalizado' => 'Se ha enviado la solicitud de registro',
                    );
                    $respuesta = $this->responseApiRepository->success($infoAdicional);

                }else{

                    $infoAdicional = array(
                        'mensajePersonalizado' => 'El cliente es menor de edad',
                    );
                    $respuesta = $this->responseApiRepository->error('420',$infoAdicional);
                }

            }




        }catch (\Exception $e) {
            $infoAdicional = array(
                'mensaje' => $e->getMessage()
            );
            $respuesta = $this->responseApiRepository->error('400',$infoAdicional);
        }
        return response()->json($respuesta, 200);
    }

    public function gestionRegistroCliente(Request $request){

        try {
            $validatedData = $this->validate($request, [
                'identificacion' => 'required|string|min:6|max:20',
                'correo' => 'required|email',
                'estado' => 'required|numeric|max:2',
            ]);

            $correo= $request->input('correo');
            $identificacion= $request->input('identificacion');
            $estado= $request->input('estado');

            $cliente = Cliente::where('correo',$correo)->where('identificacion',$identificacion)->first();
            if(isset($cliente->id)){
                switch($estado){
                    case 1:
                        $cliente->estado = 1;
                        $cliente->estado_registro=1;
                        $cliente->save();

                        $mailData = [
                            'title' => 'Bienvenid@ a Berakha',
                            'body' => $cliente->nombre. ' te damos la bienvenida a la primera plataforma donde encontraras los mejores emprendimientos de Colombia. Ya puedes ingresar a la plataforma'
                        ];

                        Mail::to($correo)->send(new RegistroClienteMail($mailData));

                        $infoAdicional = array(
                            'mensajePersonalizado' => 'Se ha habilitado el cliente',
                        );
                        $respuesta = $this->responseApiRepository->success($infoAdicional);
                        break;
                    case 2:
                        $cliente->estado = 0;
                        $cliente->estado_registro=2;
                        $cliente->save();

                        $mailData = [
                            'title' => 'Lo sentimos',
                            'body' => $cliente->nombre. ' lamentamos decirte que en estos momentos no podemos aprobar tu solicitud'
                        ];

                        Mail::to($correo)->send(new RegistroClienteMail($mailData));

                        $infoAdicional = array(
                            'mensajePersonalizado' => 'Se rechazo la solicitud del cliente',
                        );
                        $respuesta = $this->responseApiRepository->success($infoAdicional);
                        break;
                    default:

                        $infoAdicional = array(
                            'mensajePersonalizado' => 'El estado no es valido',
                        );
                        $respuesta = $this->responseApiRepository->error('420',$infoAdicional);
                        break;
                }
            }else{

                $infoAdicional = array(
                    'mensajePersonalizado' => 'El cliente no encuentra se registrado',
                );
                $respuesta = $this->responseApiRepository->error('420',$infoAdicional);
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
     *     path="/api/clientes",
     *     tags={"Clientes"},
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
     *                         property="nombre",
     *                         type="string",
     *                         example="Aderson Felix"
     *                     ),
     *                      @OA\Property(
     *                         property="email",
     *                         type="email",
     *                         example="aleacosta@.com"
     *                     ),
     *                     @OA\Property(
     *                         property="clave",
     *                         type="string",
     *                         example="123456"     *                                          
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
        return Cliente::all();
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
     * @param  \App\Http\Requests\StoreClienteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClienteRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClienteRequest  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        //
    }
}
