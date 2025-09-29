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
		<div class="col-md-8 offset-md-2">
			<div class="row">
				<div class="col text-center py-3">
					<a type="button" class="text-main" data-bs-toggle="modal" data-bs-target="#modalFormaPago">
						<i class="fa-solid fa-credit-card" style="font-size: 60px;"></i>
					</a>
					<br>
					Formas de Pago
				</div>
				<div class="col text-center py-3">
					<a type="button" class="text-main" data-bs-toggle="modal" data-bs-target="#modalRequisitos">
						<i class="fa-solid fa-list-check" style="font-size: 60px;"></i>
					</a>
					<br>
					Requisitos para Pagos del Permiso de Circulación
				</div>
				<div class="col text-center py-3">
					<a type="button" class="text-main" data-bs-toggle="modal" data-bs-target="#modalGuiaUsuario">
						<i class="fa-solid fa-chalkboard-user" style="font-size: 60px;"></i>
					</a>
					<br>
					Guía de Usuario
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container">
    <div class="row py-3">
        <div class="col-xs-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4 py-3">
            <div class="card border shadow">
                <div class="card-header text-center">
                    <h4 class="pt-2">Permiso de Circulación</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('permisocirculacion.deuda') }}" id="form-home">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="rut"><b>RUT</b> <span class="text-muted">(Sin puntos y con guión)</span></label>
                            <input type="text" name="rut" id="rut" class="form-control @error('rut') is-invalid @enderror" placeholder="Ej: 12345678-9" value="{{ old('rut') }}" minlength="9" maxlength="10" required>
                            @error('rut')
                            <p class="text-danger">{{ $errors->first('rut') }}</p>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="placa"><b>PLACA</b></label>
                            <input type="text" name="placa" id="placa" class="form-control @error('placa') is-invalid @enderror" placeholder="Ej: AABB12" value="{{ old('placa') }}" minlength="6" maxlength="6" onkeyup="this.value = this.value.toUpperCase();" required>
                            @error('placa')
                            <p class="text-danger">{{ $errors->first('placa') }}</p>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label for="email"><b>CORREO ELECTRÓNICO</b></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Ej: correo@gmail.cl" value="{{ old('email') }}" maxlength="50" required>
                            @error('email')
                            <p class="text-danger">{{ $errors->first('email') }}</p>
                            @enderror
                        </div>
                        <div class="form-group mb-2 d-grid gap-2">
                            <input type="submit" value="CONTINUAR" class="btn btn-main">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFormaPago" tabindex="-1" aria-labelledby="modalFormaPagoLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5 text-center" id="modalFormaPagoLabel">Formas de Pago</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>El medio de pago a través de internet es tarjeta de débito ó de crédito.</p>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-main" data-bs-dismiss="modal">Aceptar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalRequisitos" tabindex="-1" aria-labelledby="modalRequisitosLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5 text-center" id="modalRequisitosLabel">Requisitos para Pagos del Permiso de Circulación</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<ul>
					<li>Si usted pagó su Permiso de Circulación <?= date('Y')-1 ?> en Loncoche y dispone de Revisión Técnica y Póliza de Seguro (SOAP) vigentes, estos registros están validados en línea. Continúe su proceso de pago.</li>
					<li>Si no dispone de Póliza de Seguro (SOAP) vigente, obténgala durante su proceso de pago en línea.</li>
					<li>Si usted pago su Permiso de Circulación en otro municipio, debe solicitar por correo a <strong>permisos@muniloncoche.cl</strong> / <strong>transito@muniloncoche.cl</strong>, adjuntando un archivo digitalizado y en formato PDF o imagen JPG con el padrón del vehículo o Certificado de inscripción, el último permiso de circulación, Seguro obligatorio (SOAP) vigente Revisión técnica y gases vigente /Homologación, .Una vez actualizada la información, podrá efectuar su trámite de pago por Internet.</li>
				</ul>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-main" data-bs-dismiss="modal">Aceptar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalGuiaUsuario" tabindex="-1" aria-labelledby="modalGuiaUsuarioLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5 text-center" id="modalGuiaUsuarioLabel">Guía de Usuario</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="col py-3">
					<div class="accordion accordion-modified" id="accordionPanelsStayOpenExample">
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
									Paso #1
								</button>
							</h2>
							<div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show">
								<div class="accordion-body">
									<p>Rellene los campos necesarios: </p>
									<ul>
										<li><b>RUT</b>: Identificador del propietario del vehiculo. </li>
										<li><b>PPU</b>: La patente a consultar </li>
										<li><b>Correo Electrónico</b>: Correo electronico del dueño de la patente.</li>
									</ul>
									<br>
									<p> Ejemplo del formulario con datos ingresados: </p>
									<img src="{{ asset('assets/img/Info1.PNG') }}" class="img-fluid" alt="Ejemplo 1">
								</div>
							</div>
						</div>
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
									Paso #2
								</button>
							</h2>
							<div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
								<div class="accordion-body">
									<p>Seleccione las deudas pendientes para el pago </p>
									<br>
									<img src="{{ asset('assets/img/Info2.PNG') }}" class="img-fluid" alt="Ejemplo 2">
								</div>
							</div>
						</div>
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
									Paso #3
								</button>
							</h2>
							<div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
								<div class="accordion-body">
									<p>Confirme el pago, el cual sera llevado a transbank</p>
									<br>
									<img src="{{ asset('assets/img/Info3.PNG') }}" class="img-fluid" alt="Ejemplo 3">
								</div>
							</div>
						</div>
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
									Paso #4
								</button>
							</h2>
							<div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse">
								<div class="accordion-body">
									<p>Despues de pagar por transbank, se enviara a los datos del pago, ademas de que se le enviara correo con el detalle del pago.</p>
									<br>
									<img src="{{ asset('assets/img/Info4.PNG') }}" class="img-fluid" alt="Ejemplo 4">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-main" data-bs-dismiss="modal">Aceptar</button>
			</div>
		</div>
	</div>
</div>
@endsection