@extends('template')

@section('content')
<div class="container">
    <div class="row py-3">
        <div class="col-md-12 py-3">
            <div class="alert alert-info">
                <p>No puede realizar el pago de su permiso de circulación, esto puede deberse a alguna de las siguientes situaciones:</p>
                <ul>
                    <li>El R.U.T corresponde al antiguo propietario.</li>
                    <li>El permiso de circulación 2024 fue pagado en otra comuna, por lo tanto no figura en nuestro portal.</li>
                    <li>El vehículo presenta morosidad anterior al año 2024.</li>
                </ul>
                <p>Para actualizar la información se deberá enviar al correo electrónico transito@insico.cl adjuntando un archivo digitalizado 
                    y en formato PDF o imagen JPG con el padrón del vehículo y el último permiso de circulación. Una vez actualizada la informacion, 
                    podrá efectuar su trámite de pago por Internet.
                </p>
            </div>
            <br>
            <a href="<?= route('permisocirculacion.index') ?>" class="btn btn-light">VOLVER</a>
        </div>
    </div>
</div>
@endsection