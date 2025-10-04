@extends('template')

@push('scripts')
<script type="text/javascript">
    function ticket_anos_ant() {
        let form = document.form_pago;
        let elems = form.elements;
        let b = new Array ();
        let suma = 0;
        let total = 0;
        let cantidad_total = 0;
        let cantidad_ticket = 0;
        let elObjeto = new Object();
        let laCadena = new String();
        laCadena='';
        let multa = document.getElementById("montomulta").value;
        let montopermiso = document.getElementById("montopermiso");
        let span_total_input = document.getElementById('total_input');
        let id_btn_pago = document.getElementById('id_submit_form_pago');

        span_total_input.textContent = 0;
        id_btn_pago.disabled = true;

        for (let i = 0 ; i < elems.length ; i++) {
            if (elems[i].type == 'checkbox' && !elems[i].checked && elems[i].name=='pago_ant[]') {
                if (form.elements[i+1].checked || !elems[i+1].disabled) {
                    elems[i+1].disabled = true;
                    form.elements[i+1].checked=false;
                }
            }
        }

        for (let i = 0 ; i < elems.length ; i++) {
            if (elems[i].type == 'checkbox' && elems[i].checked && elems[i].name=='pago_ant[]') {
                elems[i].checked = true;
                suma = (parseInt(span_total_input.textContent)+parseInt(elems[i].value));
                elObjeto = elems[i];
                laCadena= elems[i].id+","+laCadena;
                elems[i+1].disabled = false;
            }
            span_total_input.textContent = suma;
            montopermiso.value = suma;
            id_btn_pago.disabled = false;
        }


        for (let i = 0 ; i < elems.length ; i++) {
            if (elems[i].type == 'checkbox'){
                cantidad_total = cantidad_total+1;
            }
            if (elems[i].type == 'checkbox' && elems[i].checked){
                cantidad_ticket = cantidad_ticket+1;
            }
        }

        if (cantidad_total==cantidad_ticket) {
            for (let i = 0 ; i < elems.length ; i++) {
                if (elems[i].type == 'radio'){
                    elems[i].disabled = false;
                }
            }
        } else {
            for (let i = 0 ; i < elems.length ; i++) {
                if (elems[i].type == 'radio') {
                    elems[i].disabled = true;
                    elems[i].checked = false;
                }
                if ((elems[i].type == 'radio' && elems[i].checked) || (elems[i].type == 'checkbox' && elems[i].checked && elems[i].name=='pago_ant')) {
                    elObjeto2 = elems[i];
                }
            }
        }
    }

    function valida_ok_permiso() {
        let form = document.form_pago;
        let elems = form.elements;
        let cantidad_total = 0;
        let cantidad_ticket = 0;
        let suma_permiso=0;
        let elObjeto = new Object();
        let montopermiso = document.getElementById('montopermiso');
        let span_total_input = document.getElementById('total_input');
        let id_btn_pago = document.getElementById('id_submit_form_pago');

        span_total_input.textContent = 0;
        id_btn_pago.disabled = true;


        for (let i = 0 ; i < elems.length ; i++) {
            if (elems[i].type == 'checkbox') {
                cantidad_total = cantidad_total+1;
            }
            if (elems[i].type == 'checkbox' && elems[i].checked) {
                cantidad_ticket = cantidad_ticket+1;
            }
        }

        if (cantidad_total==cantidad_ticket) {
            for (let i = 0 ; i < elems.length ; i++) {
                if ((elems[i].type == 'radio' && elems[i].checked)||(elems[i].type == 'checkbox' && elems[i].checked && elems[i].name=='pago_ant[]')) {
                    suma_permiso = (parseInt(span_total_input.textContent)+parseInt(elems[i].value));
                    elObjeto = elems[i];
                }
                span_total_input.textContent = suma_permiso;
                montopermiso.value = suma_permiso;
            }
        } else {
            for (let i = 0 ; i < elems.length ; i++) {
                if (elems[i].type == 'radio') {
                    elems[i].disabled = true;
                    elems[i].checked = false;
                }
            }
        }

        if (suma_permiso > 0) {
            id_btn_pago.disabled = false;
        } else {
            id_btn_pago.disabled = true;
        }
    }
</script>

<script>
    $(document).ready(function () {
        $('#form_pago').submit(function () {
            if ($('#montopermiso').val() == '' || $('#montopermiso').val() == 0 || $('#montopermiso').val() == '0') {
                alert('Seleccione un item a pagar para continuar.');
                $('#id_submit_form_pago').prop('disabled', true);
                return false;
            } else {
                $('#id_submit_form_pago').prop('disabled', false);
            }
        });
    });
</script>
@endpush

@section('content')
<div class="container">
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card card-default">
                <div class="card-header">
                    <div class="card-title">
                        Datos del Propietario
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <tbody>
                                <tr>
                                    <th style="border:0px;"><i class="fa-solid fa-user"></i> <?= trim($propietario->prop_nombre) . ' ' . trim($propietario->prop_app) . ' ' . trim($propietario->prop_apm) ?></th>
                                </tr>
                                <tr>
                                    <th style="border:0px;"><i class="fa-solid fa-address-card"></i> <?= trim($propietario->prop_rut) . '-' . trim($propietario->prop_rut_dv) ?></th>
                                </tr>
                                <tr>
                                    <th style="border:0px;"><i class="fa-solid fa-house"></i> <?= trim($propietario->prop_direccion) ?></th>
                                </tr>
                                <tr>
                                    <th style="border:0px;"><i class="fa-solid fa-location-pin"></i> <?= trim($propietario->prop_comuna) ?></th>
                                </tr>
                                <tr>
                                    <th style="border:0px;"><i class="fa-solid fa-phone"></i> <?= trim($propietario->prop_telefono) ?></th>
                                </tr>
                                <tr>
                                    <th style="border:0px;"><i class="fa-solid fa-at"></i> <?= trim($propietario->prop_correo) ?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-default">
                <div class="card-header">
                    <div class="card-title">
                        Datos del Vehículo - <strong><?= strtoupper($placa) ?></strong>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <tbody>
                                <tr>
                                    <td style="border:0px;"><b>Tipo:</b> <?= trim($propietario->veh_tipo) ?></td>
                                    <td style="border:0px;"><b>Combustible:</b> <?= trim($propietario->veh_combustible) ?></td>
                                </tr>
                                <tr>
                                    <td style="border:0px;"><b>Marca:</b> <?= trim($propietario->veh_marca) ?></td>
                                    <td style="border:0px;"><b>Transmisión:</b> <?= trim($propietario->veh_transmision) ?></td>
                                </tr>
                                <tr>
                                    <td style="border:0px;"><b>Modelo:</b> <?= trim($propietario->veh_modelo) ?></td>
                                    <td style="border:0px;"><b>Equipamiento:</b> <?= trim($propietario->veh_equipamiento) ?></td>
                                </tr>
                                <tr>
                                    <td style="border:0px;"><b>Color:</b> <?= trim($propietario->veh_color) ?></td>
                                    <td style="border:0px;"><b>Año Fab.:</b> <?= trim($propietario->veh_ano_fab) ?> <b>- Sello:</b> <?= trim($propietario->veh_sello) ?></td>
                                </tr>
                                <tr>
                                    <td style="border:0px;"><b>N° Motor:</b> <?= trim($propietario->veh_nro_motor) ?></td>
                                    <td style="border:0px;"><b>Puertas:</b> <?= trim($propietario->veh_puertas) ?> <b>- Asientos:</b> <?= trim($propietario->veh_asientos) ?></td>
                                </tr>
                                <tr>
                                    <td style="border:0px;"><b>N° Chasis:</b> <?= trim($propietario->veh_nro_chasis) ?></td>
                                    <td style="border:0px;"><b>C.C.:</b> <?= trim($propietario->veh_ccubicos) ?> <b>- Carga:</b> <?= trim($propietario->veh_carga) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('permisocirculacion.confirmacion') }}" method="POST" name="form_pago" id="form_pago" class="form-inline">
        @csrf
        <input type="hidden" name="rut" id="rut" value="<?= $rut ?>">
        <input type="hidden" name="placa" id="placa" value="<?= $placa ?>">
        <input type="hidden" name="email" id="email" value="<?= $email ?>">
        <input type="hidden" name="montomulta" id="montomulta" value="<?= $monto_multa ?>">
        <input type="hidden" name="montopermiso" id="montopermiso" value="0">
        <div class="row">
            <?php if ($permisos_anterior): ?>
                <div class="col-md-12">
                    <h3 class="text-center">Deuda Permiso Anterior</h3>
                    <hr>
                    <br>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <th>Selección</th>
                                <th>Placa</th>
                                <th>Periodo</th>
                                <th>Tipo Pago</th>
                                <th class="d-none d-md-table-cell">Neto</th>
                                <th class="d-none d-md-table-cell">C. Monetaria</th>
                                <th class="d-none d-md-table-cell">Interés</th>
                                <th class="d-none d-md-table-cell">Multa</th>
                                <th style="width: 120px; overflow: auto;">Subtotal</th>
                            </thead>
                            <tbody>
                                <?php foreach ($permisos_anterior as $row): ?>
                                    <tr>
                                        <td><input type="checkbox" name="pago_ant[]" value="<?= $row->pago_total_calculado.'_'.$row->placa_veh.'_'.$row->tipo_cargo.'_'.$row->ano_cargo ?>" id="<?= $row->ano_cargo.'_'.$row->tipo_cargo.'_'.$row->placa_veh ?>" onclick="javascript:return ticket_anos_ant();"></td>
                                        <td><?= $row->placa_veh ?></td>
                                        <td><?= $row->ano_cargo ?></td>
                                        <td><strong><?= format_tipo_cargo($row->tipo_cargo);?></strong></td>
                                        <td class="d-none d-md-table-cell">$ <?= format_clp($row->pago_monto_neto) ?></td>
                                        <td class="d-none d-md-table-cell">$ <?= format_clp($row->pago_correccion) ?></td>
                                        <td class="d-none d-md-table-cell">$ <?= format_clp($row->pago_interes) ?></td>
                                        <td class="d-none d-md-table-cell">$ <?= format_clp($row->pago_multa) ?></td>
                                        <td>$ <?= format_clp($row->pago_total_calculado) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <br>
        <div class="row">
            <?php if ($permisos_actual): ?>
                <div class="col-md-12">
                    <h3 class="text-center">Deuda Permiso Actual</h3>
                    <hr>
                    <br>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <th>Selección</th>
                                <th>Placa</th>
                                <th>Periodo</th>
                                <th>Tipo Pago</th>
                                <th class="d-none d-md-table-cell">Neto</th>
                                <th class="d-none d-md-table-cell">C. Monetaria</th>
                                <th class="d-none d-md-table-cell">Interés</th>
                                <th class="d-none d-md-table-cell">Multa</th>
                                <th style="width: 120px; overflow: auto;">Subtotal</th>
                            </thead>
                            <tbody>
                                <?php foreach ($permisos_actual as $row): ?>
                                    <tr>
                                        <td><input class="checkbox-pagos" type="radio" name="check-pago" onclick="javascript:return valida_ok_permiso();" value="<?= $row->pago_total_calculado.'_'.$row->placa_veh.'_'.$row->tipo_cargo.'_'.$row->ano_cargo ?>"></td>
                                        <td><?= $row->placa_veh ?></td>
                                        <td><?= $row->ano_cargo ?></td>
                                        <td><strong><?= format_tipo_cargo($row->tipo_cargo);?></strong></td>
                                        <td class="d-none d-md-table-cell">$ <?= format_clp($row->pago_monto_neto) ?></td>
                                        <td class="d-none d-md-table-cell">$ <?= format_clp($row->pago_correccion) ?></td>
                                        <td class="d-none d-md-table-cell">$ <?= format_clp($row->pago_interes) ?></td>
                                        <td class="d-none d-md-table-cell">$ <?= format_clp($row->pago_multa) ?></td>
                                        <td>$ <?= format_clp($row->pago_total_calculado) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-end" style="font-size: 20px;"><strong>Multas de Tránsito: $<span ><?= $monto_multa ?></span></strong></p>
                        <p class="text-end" style="font-size: 20px;"><strong>Permiso de Circulación: $<span id="total_input">0</span></strong></p>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-auto">
                        <a href="<?= route('permisocirculacion.index') ?>" class="btn btn-light">VOLVER</a>
                    </div>
                    <div class="col-auto">
                        <button type="submit" id="id_submit_form_pago" class="btn btn-main" disabled>CONTINUAR <i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection