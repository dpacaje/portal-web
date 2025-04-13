<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
        <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
        <script src="{{ asset('assets/fontawesome/js/all.min.js') }}"></script>
        
        <link rel="stylesheet" href="{{ asset('assets/css/mapp.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
        <script src="{{ asset('assets/js/scripts.js') }}"></script>

    </head>
    <body>

        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img alt="Admin" src="{{ asset('assets/img/logo.png') }}" width="150" height="60">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="https://plataformafirma.ecertchile.cl/SitioWeb/Consultas/ConsultaDoc.aspx" target="_blank" >
                                <i class="fa-regular fa-circle-check"></i>
                                Validar Documento Electrónico
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <br>

        @yield('content')

        <br>

        <footer>
            <span>SISTEMA DESARROLLADO POR <a href="https://www.insicosa.cl" target="_blank">INSICO S.A.</a> / TODOS LOS DERECHOS RESERVADOS 2025</span>
        </footer>

    </body>
</html>
