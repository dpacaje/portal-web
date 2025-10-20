@extends('template')

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
            <div class="alert alert-success">
                El comprobante fue enviado a correo electrónico, revise su bandeja de entrada o SPAM. En caso contrario comuníquese con la municipalidad.
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
            <h3>Transacción Finalizada</h3>
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
                        <th>Código de Autorización</th>
                        <td>{{ $data->TBK_CODIGO_AUTORIZACION }}</td>
                    </tr>
                    <tr>
                        <th>Fecha de Pago</th>
                        <td>{{ $data->TBK_FECHA_TRANSACCION }}</td>
                    </tr>
                    <tr>
                        <th>Monto Pagado</th>
                        <td>{{ format_clp($data->TBK_MONTO) }}</td>
                    </tr>
                    <tr>
                        <th>Modalidad de Pago</th>
                        <td>{{ $data->TBK_TIPO_PAGO }}</td>
                    </tr>
                    <tr>
                        <th>Número de Cuotas</th>
                        <td>{{ $data->TBK_NUMERO_CUOTAS }}</td>
                    </tr>
                    <tr>
                        <th>Comprobante de Pago</th>
                        <td><a href="{{ route('permisocirculacion.reimprime') }}" class="link-primary">Descargar</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2 text-end">
            <a href="{{ route('permisocirculacion.index') }}" class="btn btn-main">FINALIZAR</a>
        </div>
    </div>
@endsection