<!DOCTYPE html>
<html>
<head>
    <title>Inspeccion</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

</head>

<body>

    <table class="table table-bordered">

        <tbody>
          <tr>

            <th style="width:30%;">
                <img src="{{ storage_path('app/imagenes/logoberakha.png') }}" style="width: 180px; height: 80px">
            </th>
            <td class="align-middle" style="width:40%;">
                <h4 class="mb-0 font-weight-semibold">INFORME GERENCIAL</h4>
            </td>
            <td style="width:30%;">
                <img src="{{ storage_path('app/imagenes/ucat.jpg') }}" style="width: 250px; height: 80px">
            </td>
          </tr>

          <tr>
            <td colspan="3">
                <p class="mb-0 text-muted">A continuación:</p>
            </td>
          </tr>

        </tbody>
      </table>

      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="card border-secondary mb-4">
                <div class="card-body">
                    <h5 class="card-title">Resumen (conteo de usuarios por acción realizada)</h5>
                    <hr width="100%" />
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Empredimiento</th>
                                    <th scope="col"># Visualizaciones</th>
                                    <th scope="col"># Comentarios</th>
                                    <th scope="col"># Calificaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resumenEmpredimientos as $dato)
                                <tr class="table">
                                    <td>{{$dato['nombre']}}</td>
                                    <td>{{$dato['conteoVisualizaciones']}}</td>
                                    <td>{{$dato['conteoComentarios']}}</td>
                                    <td>{{$dato['conteoCalificaciones']}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <p class="mb-0 text-muted">A continuación se encontrará el detalle de los empredimientos:</p>

      @foreach ($datosEmprendimientos->emprendimientos as $empredimiento)
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="card border-secondary mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{$empredimiento->nombre_emprendimiento}}</h5>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <p class="mb-0 text-muted">Visualizaciones por cliente:</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Cliente</th>
                                                <th scope="col"># Visualizaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($empredimiento->visualizaciones as $visualizacion)
                                            <tr class="table">
                                                <td>{{$visualizacion->cliente->nombre}}</td>
                                                <td>{{$visualizacion->conteo}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                 <p class="mb-0 text-muted">Comentarios por cliente:</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Cliente</th>
                                                <th scope="col">Comentario</th>
                                                <th scope="col">Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($empredimiento->comentarios as $comentariod)
                                            <tr class="table">
                                                <td>{{$comentariod->cliente->nombre}}</td>
                                                <td>{{$comentariod->comentario}}</td>
                                                <td>{{$comentariod->updated_at}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                 <p class="mb-0 text-muted">Calificaciones por cliente:</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Cliente</th>
                                                <th scope="col">Calificacion</th>
                                                <th scope="col">Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($empredimiento->calificaciones as $calificaciond)
                                            <tr class="table">
                                                <td>{{$calificaciond->cliente->nombre}}</td>
                                                <td>{{$calificaciond->calificacion}}</td>
                                                <td>{{$calificaciond->updated_at}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
      @endforeach

</body>
</html>
