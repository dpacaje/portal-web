@extends('template')

@section('content')

    <div class="container py-4" style="max-width: 960px; width: 100%;">
        <div class="card p-4 mb-4">
            <div class="mb-4">
                <h2 class="fw-bold">Hola vecino/a 👋</h2>
                <p class="text-muted fs-5">
                    Bienvenido/a a la plataforma de pagos online. Acá podrás realizar el pago de tus documentos, de forma rápida, segura y sin complicaciones.
                </p>
                <p class="text-muted">
                    Desde aquí podrás acceder a distintos tipos de documentos como facturas, boletas, multas y más, según los servicios habilitados para ti. Cada sección te permitirá revisar el estado de tu deuda, verificar fechas de vencimiento y realizar pagos electrónicos de manera segura.
                </p>
                <p class="text-muted">
                    Usa los accesos rápidos a continuación para comenzar.
                </p>
            </div>

            <ul class="list-group list-group-flush border-0">
                <li class="list-group-item px-3 py-3 d-flex justify-content-between align-items-center bg-white border-start border-3 border-secondary-subtle rounded-2 shadow-sm mb-2">
                    <div class="d-flex align-items-center">
                        <span class="fs-4 me-3"><i class="fa-solid fa-car"></i></span>
                        <span class="fw-semibold text-dark">Permiso Circulación</span>
                    </div>
                    <a href="{{ route('permisocirculacion.index') }}" class="btn btn-sm btn-outline-dark px-3">Acceder</a>
                </li>
                <li class="list-group-item px-3 py-3 d-flex justify-content-between align-items-center bg-white border-start border-3 border-secondary-subtle rounded-2 shadow-sm mb-2">
                    <div class="d-flex align-items-center">
                        <span class="fs-4 me-3"><i class="fa-regular fa-bookmark"></i></span>
                        <span class="fw-semibold text-dark">Patentes Municipales</span>
                    </div>
                    <a href="{{ route('patentemunicipal.index') }}" class="btn btn-sm btn-outline-dark px-3">Acceder</a>
                </li>
                <li class="list-group-item px-3 py-3 d-flex justify-content-between align-items-center bg-white border-start border-3 border-secondary-subtle rounded-2 shadow-sm mb-2">
                    <div class="d-flex align-items-center">
                        <span class="fs-4 me-3"><i class="fa-solid fa-sack-xmark"></i></span>
                        <span class="fw-semibold text-dark">Derechos de Aseo</span>
                    </div>
                    <a href="{{ route('derechoaseo.index') }}" class="btn btn-sm btn-outline-dark px-3">Acceder</a>
                </li>
            </ul>
        </div>
    </div>

@endsection