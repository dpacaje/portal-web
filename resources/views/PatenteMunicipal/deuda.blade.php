@extends('template')

@push('scripts')
<script type="text/javascript">
    function sumar() {
        let form = document.form_pago;
        let elems = form.elements;
        let suma_patente = 0;
        let hdnpatente = document.getElementById('hdnpatente');
        let span_total_input = document.getElementById('total_input');
        let id_btn_pago = document.getElementById('id_submit_form_pago');

        hdnpatente.value = 0;
        span_total_input.textContent = 0;
        id_btn_pago.disabled = true;

        for (let i = 0 ; i < elems.length ; i++) {
            if (typeof elems[i] !== 'undefined' && elems[i].type == 'checkbox' && elems[i].name=='check_patente[]' && !elems[i].checked) {
                if (elems[i+1].checked || !elems[i+1].disabled) {
                    elems[i+1].checked = false;
                    elems[i+1].disabled = true;
                }
            }
        }

        for (let i = 0 ; i < elems.length ; i++) {
            if (typeof elems[i] !== 'undefined' && elems[i].type == 'checkbox' && elems[i].name=='check_patente[]') {
                if (elems[i].checked) {
                    let valores = elems[i].value.split('_',4);
                    let subtotal = valores[3];
                    suma_patente += parseInt(subtotal);
                    span_total_input.textContent = suma_patente;
                    hdnpatente.value = suma_patente;

                    elems[i+1].disabled = false;
                }
            }
        }

        if (suma_patente > 0) {
            id_btn_pago.disabled = false;
        } else {
            id_btn_pago.disabled = true;
        }
    }
</script>

<script>
    $(document).ready(function () {
        $('#form_pago').submit(function () {
            if ($('#hdnpatente').val() == '' || $('#hdnpatente').val() == 0 || $('#hdnpatente').val() == '0') {
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
            <h2><i class="fa-solid fa-user"></i> {{ $propietario->nombre_contribuyente }}</h2>
            <h4><i class="fa-solid fa-id-card"></i> {{ $propietario->rut . '-' . $propietario->dv }}</h4>
            <h4><i class="fa-solid fa-location-dot"></i> {{ $propietario->nom_calle }}</h4>
            <h4><i class="fa-solid fa-house-user"></i> {{ $propietario->rol }}</h4>
            <h4><i class="fa-solid fa-at"></i> {{ $email }}</h4>
        </div>
    </div>

    <form action="#" method="POST" name="form_pago" id="form_pago" class="form-inline">
        @csrf
        <input type="hidden" name="hdnrut" id="hdnrut" value="{{ $rut }}">
        <input type="hidden" name="hdnrol" id="hdnrol" value="{{ $rol }}">
        <input type="hidden" name="hdncorreo" id="hdncorreo" value="{{ $email }}">
        <input type="hidden" name="hdnpatente" id="hdnpatente" value="0">
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
                                <th>Vencimiento</th>
                                <th>Año</th>
                                <th>Semestre</th>
                                <th>Rol</th>
                                <th class="d-none d-md-table-cell">Neto</th>
                                <th class="d-none d-md-table-cell">Interés</th>
                                <th class="d-none d-md-table-cell">Multa</th>
                                <th style="width: 120px; overflow: auto;">Subtotal</th>
                            </thead>
                            <tbody>
                                @foreach ($deuda as $row)
                                    @php
                                        $total = ($row->interes + $row->multa + $row->total);
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" name="check_patente[]" onclick="javascript:return sumar();" value="{{ $row->ano.'_'.$row->sem.'_'.$row->rol.'_'.$total }}"></td>
                                        <td>{{ vencimiento($row->ano, $row->sem, 1) }}</td>
                                        <td>{{ parseAnioToSemestre($row->ano, $row->sem) }}</td>
                                        <td>{{ parseCuotaToSemestre($row->sem) }}</td>
                                        <td>{{ $row->rol }}</td>
                                        <td class="d-none d-md-table-cell">$ {{ format_clp($row->total) }}</td>
                                        <td class="d-none d-md-table-cell">$ {{ format_clp($row->interes) }}</td>
                                        <td class="d-none d-md-table-cell">$ {{ format_clp($row->multa) }}</td>
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
                        <a href="{{ route('patentemunicipal.index') }}" class="btn btn-light">VOLVER</a>
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