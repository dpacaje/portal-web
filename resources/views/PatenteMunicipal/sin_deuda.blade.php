@extends('template')

@section('content')
<div class="container">
    <div class="row py-3">
        <div class="col-md-12 py-3">
            <div class="alert alert-info">
                <p>No se ha encontrado información relacionada con el ROL ingresado, esto puede deberse a la siguientes situaciones.</p>
                <ul>
                    <li>El ROL ingresado está digitado erróneamente.</li>
                    <li>El ROL pertenece a Patentes que no pueden ser pagados por este Portal.</li>
                </ul>
            </div>
            <br>
            <a href="<?= route('patentemunicipal.index') ?>" class="btn btn-light">VOLVER</a>
        </div>
    </div>
</div>
@endsection