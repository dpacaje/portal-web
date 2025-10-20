@extends('template')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
        <div class="alert alert-warning">
            <p>Estimad@ contribuyente, su transacción fue rechazada y los posibles motivos puede ser:</p>
            <ul>
                <li>Error en el ingreso de datos de la transacción.</li>
                <li>Error en el ingreso de parámetros de la tarjeta y/o cuenta de usuario.</li>
                <li>Error por exceder el monto máximo.</li>
                <li>Error de conexión.</li>
            </ul>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
        <h3>Transacción Rechazada</h3>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Medio de Pago</th>
                    <td>WEBPAY</td>
                </tr>
                <tr>
                    <th>Número de Transacción</th>
                    <td>{{ $data->pago_id }}</td>
                </tr>
                <tr>
                    <th>Fecha de Emisión</th>
                    <td>{{ $data->TBK_FECHA_TRANSACCION }}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>{{ $data->estado }}</td>
                </tr>
            </table>
        </div>
        <a href="{{ route('home') }}" class="btn btn-secondary">VOLVER AL INICIO</a>
    </div>
</div>
@endsection