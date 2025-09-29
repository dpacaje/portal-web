@extends('template')

@push('scripts')
<script type="text/javascript">
    function sumar() {
        let form = document.form_pago;
        let elems = form.elements;
        let suma_aseo = 0;
        let hdnaseo = document.getElementById('hdnaseo');
        let span_total_input = document.getElementById('total_input');
        let id_btn_pago = document.getElementById('id_submit_form_pago');

        hdnaseo.value = 0;
        span_total_input.textContent = 0;
        id_btn_pago.disabled = true;

        for (let i = 0 ; i < elems.length ; i++) {
            if (typeof elems[i] !== 'undefined' && elems[i].type == 'checkbox' && elems[i].name=='check_aseo[]' && !elems[i].checked) {
                if (elems[i+1].checked || !elems[i+1].disabled) {
                    elems[i+1].checked = false;
                    elems[i+1].disabled = true;
                }
            }
        }

        for (let i = 0 ; i < elems.length ; i++) {
            if (typeof elems[i] !== 'undefined' && elems[i].type == 'checkbox' && elems[i].name=='check_aseo[]') {
                if (elems[i].checked) {
                    let valores = elems[i].value.split('_',3);
                    let subtotal = valores[1];
                    suma_aseo += parseInt(subtotal);
                    span_total_input.textContent = suma_aseo;
                    hdnaseo.value = suma_aseo;

                    elems[i+1].disabled = false;
                }
            }
        }

        if (suma_aseo > 0) {
            id_btn_pago.disabled = false;
        } else {
            id_btn_pago.disabled = true;
        }
    }
</script>

<script>
    $(document).ready(function () {
        $('#form_pago').submit(function () {
            if ($('#hdnaseo').val() == '' || $('#hdnaseo').val() == 0 || $('#hdnaseo').val() == '0') {
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
        <div class="col-md-12">
            <h2><i class="fa-solid fa-user"></i> {{ $propietario->nombre }}</h2>
            <h4><i class="fa-solid fa-id-card"></i> {{ $propietario->rut . '-' . $propietario->rut_dv }}</h4>
            <h4><i class="fa-solid fa-location-dot"></i> {{ $propietario->direccion }}</h4>
            <h4><i class="fa-solid fa-house-user"></i> {{ $propietario->rol . '-' . $propietario->rol_dv }}</h4>
            <h4><i class="fa-solid fa-at"></i> {{ $email }}</h4>
        </div>
    </div>

    <form action="#" method="POST" name="form_pago" id="form_pago" class="form-inline">
        @csrf
        <input type="hidden" name="hdnrol" id="hdnrol" value="<?= $rol ?>">
        <input type="hidden" name="hdnroldv" id="hdnroldv" value="<?= $roldv ?>">
        <input type="hidden" name="hdncorreo" id="hdncorreo" value="<?= $email ?>">
        <input type="hidden" name="hdnaseo" id="hdnaseo" value="0">
        <div class="row">
            @if ($deuda)
                <div class="col-md-12">
                    <h3 class="text-center">Deuda Actual</h3>
                    <hr>
                    <br>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <th>Selección</th>
                                <th>Año</th>
                                <th>Cuota</th>
                                <th>Vencimiento</th>
                                <th class="d-none d-md-table-cell">Neto</th>
                                <th class="d-none d-md-table-cell">Interés</th>
                                <th class="d-none d-md-table-cell">Multa</th>
                                <th style="width: 120px; overflow: auto;">Subtotal</th>
                            </thead>
                            <tbody>
                                @foreach ($deuda as $row)
                                    @php
                                        $total = ($row->interes_pagado + $row->multa_pagado + $row->valor_cuota);
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" name="check_aseo[]" onclick="javascript:return sumar();" value="{{ $row->ano.'_'.$total.'_'.$row->cuota.'_'.$row->rol.'_'.$row->rol_dv }}"></td>
                                        <td>{{ $row->ano }}</td>
                                        <td>{{ $row->cuota }}</td>
                                        <td>{{ $row->fec_vcto }}</td>
                                        <td class="d-none d-md-table-cell">$ {{ format_clp($row->valor_cuota) }}</td>
                                        <td class="d-none d-md-table-cell">$ {{ format_clp($row->interes_pagado) }}</td>
                                        <td class="d-none d-md-table-cell">$ {{ format_clp($row->multa_pagado) }}</td>
                                        <td>$ {{ format_clp($total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-end" style="font-size: 20px;"><strong>Total a Pagar: $<span id="total_input">0</span></strong></p>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-auto">
                        <a href="<?= route('derechoaseo.index') ?>" class="btn btn-light">VOLVER</a>
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