@extends('template')

@push('scripts')
<script>
    $(document).ready(function(){
        $('form#form-home').on('submit', function(){
            let rut = $('#rut').val();
            let placa = $('#placa').val();
            let email = $('#email').val();
            let rutRegex = /^\d{7,8}-[\dKk]$/;
            let placaRegex = /^[a-zA-Z]{2,4}[0-9]{2,4}$/;
            let regex = /^([a-zA-Z0-9_.+-]+)@([a-zA-Z0-9-]+)\.([a-zA-Z]{2,6})$/;
            let status = false;
            let message = '';

            if (rut == '') {
                status = false;
                message = 'Debe ingresar el Rut.';
            } else if (!rutRegex.test(rut)) {
                status = false;
                message = 'El RUT no cumple el formato solicitado.';
            } else if (placa == '') {
                status = false;
                message = 'Debe ingresar la Placa.';
            } else if (!placaRegex.test(placa)) {
                status = false;
                message = 'La Placa no cumple el formato solicitado.';
            } else if (!regex.test(email)) {
                status = false;
                message = 'El Correo Electrónico no es válido.';
            } else {
                status = true;
                message = '';
            }

            if (!status) {
                Swal.fire('', message, 'error');
                return false;
            }
        });
    });
</script>
@endpush

@section('content')
<div class="container">
    <div class="row py-3">
        <div class="col-md-6 py-3">
            <div class="card border shadow">
                <div class="card-header bg-transparent border-0">
                    <h5>Ingrese su RUT, la Placa del Vehículo y un Correo Electrónico</h5>
                    <span class="text-muted">*El correo electrónico es requerido para poder enviarle la información del pago realizado.</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('permisocirculacion.deuda') }}" id="form-home">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="rut"><b>RUT</b></label>
                            <input type="text" name="rut" id="rut" class="form-control @error('rut') is-invalid @enderror" placeholder="Ej: 12345678-9" value="<?= old('rut') ?>" maxlength="10">
                            <span class="text-muted">(Sin puntos y con guión)</span>
                            @error('rut')
                            <p class="text-danger">{{ $errors->first('rut') }}</p>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="placa"><b>PLACA</b></label>
                            <input type="text" name="placa" id="placa" class="form-control @error('placa') is-invalid @enderror" placeholder="Ej: AABB12" value="<?= old('placa') ?>" maxlength="6" onkeyup="this.value = this.value.toUpperCase();">
                            <span class="text-muted">(Si es una moto, debe ingresarla con el cero, ej: ABC012)</span>
                            @error('placa')
                            <p class="text-danger">{{ $errors->first('placa') }}</p>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label for="email"><b>CORREO ELECTRÓNICO</b></label>
                            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Ej: correo@gmail.cl" value="<?= old('email') ?>">
                            @error('email')
                            <p class="text-danger">{{ $errors->first('email') }}</p>
                            @enderror
                        </div>
                        <div class="form-group mb-2 d-grid gap-2">
                            <input type="submit" value="CONTINUAR" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 py-3">
            <div class="accordion accordion-modified" id="accordionPanelsStayOpenExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                        Información General #1
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                        Validar tu Comprobante #2
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                        Accordion Item #3
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection