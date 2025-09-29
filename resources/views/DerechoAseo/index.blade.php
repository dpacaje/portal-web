@extends('template')

@push('scripts')
<script>
    $(document).ready(function(){
        $('form#form-home').on('submit', function(){
			let rol = $('#rol').val();
            let roldv = $('#roldv').val();
            let email = $('#email').val();
            let regex = /^([a-zA-Z0-9_.+-]+)@([a-zA-Z0-9-]+)\.([a-zA-Z]{2,6})$/;
            let status = false;
            let message = '';

            if (rol == '') {
                status = false;
                message = 'Debe ingresar el Rol (Manzana).';
            } else if (roldv == '') {
                status = false;
                message = 'Debe ingresar el Rol (Predio).';
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
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-8">
			<div class="accordion accordion-modified" id="accordionExample">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							Información
						</button>
					</h2>
					<div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
						<div class="accordion-body">
							<strong>Estimado Contribuyente:</strong>
							<p>Durante los meses de Abril, Julio Agosto y Enero de cada año, corresponde pagar los Derechos de Aseo domiciliario. Encarecemos a Ud. cumplir con esta obligación que nos permite mantener nuestro Servicio de Aseo comunal.</p>
							<p>Si no conoce el número de rol de avalúo, puede buscarlo en el <a href="https://zeus.sii.cl/avalu_cgi/br/brc110.sh?" target="_blank">Servicio de Impuestos Internos (SII)</a>. En el link Buscar una Propiedad Asociada al RUT (se requiere clave tributaria o clave única).</p>
							<p>Para otras consultas puede llamar al teléfono: <strong>987654321</strong>, o escribir al e-mail <strong>rentas.daf@gorbea.cl</strong></p>
							<p><strong>Recomendación: Ingrese su rol (Manzana y Predio) sin ceros antes de su numeración. Por ejemplo el rol 000123-0010, debe ingresarlo como 123-10.</strong></p>
						</div>
					</div>
				</div>
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
							¿Cómo puedo saber si la firma electrónica de mi documento es válida?
						</button>
					</h2>
					<div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
						<div class="accordion-body">
							<a href="https://plataformafirma.ecertchile.cl/SitioWeb/Consultas/ConsultaDoc.aspx" target="_blank">https://plataformafirma.ecertchile.cl/SitioWeb/Consultas/ConsultaDoc.aspx</a>
						</div>
					</div>
				</div>
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFourh" aria-expanded="false" aria-controls="collapseFourh">
							Guía de Usuario
						</button>
					</h2>
					<div id="collapseFourh" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
						<div class="accordion-body">
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
				</div>
			</div>
		</div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
            <div class="card border shadow">
                <div class="card-header text-center">
                    <h4 class="pt-2">Derechos Aseo</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('derechoaseo.deuda') }}" id="form-home">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="rol"><b>ROL</b> <span class="text-muted">(Manzana-Predio)<span></label>
							<div class="row">
								<div class="col-6">
									<input type="number" name="rol" id="rol" class="form-control @error('rol') is-invalid @enderror" placeholder="Ej: 123 (manzana)" value="{{ old('rol') }}" min="1" max="99999999" required>
								</div>
								<div class="col-6">
									<input type="number" name="roldv" id="roldv" class="form-control @error('roldv') is-invalid @enderror" placeholder="Ej: 4 (predio)" value="{{ old('roldv') }}" min="1" max="99999999" required>
								</div>
							</div>
                            @error('rol')
                            <p class="text-danger">{{ $errors->first('rol') }}</p>
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
@endsection