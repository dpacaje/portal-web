@extends('template')

@push('scripts')
<script>
    $(document).ready(function () {
    });
</script>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h3>Confirmación de Pago</h3>
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Usted está pagando</th>
                        <td><?= config('envar.APP_TIPO') ?></td>
                    </tr>
                    <tr>
                        <th>Usted está pagando en</th>
                        <td>Pesos Chilenos, Santiago, Chile.</td>
                    </tr>
                    <tr>
                        <th>Url Comercio</th>
                        <td><a href="<?= route('permisocirculacion.index') ?>" class="link-primary"><?= config('envar.APP_TIPO') . ' - ' . config('envar.APP_CLIENTE') ?></a></td>
                    </tr>
                    <tr>
                        <th>Fecha y Hora de emisión</th>
                        <td><?= date('d-m-Y H:i:s') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h3>Resumen de Pago</h3>
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Origen de Pago</th>
                            <th>Detalle</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($permisos_anteriores): ?>
                            <?php foreach ($permisos_anteriores as $row): ?>
                                <?php $pago = explode('_', $row); ?>
                                <tr>
                                    <td>Permiso Circulación</td>
                                    <td>El pago corresponde a <?= format_tipo_cargo($pago[2]) ?> de la PPU <?= $pago[1] ?> del año <?= $pago[3] ?></td>
                                    <td class="text-end">$<?= format_clp($pago[0]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if ($permisos_actuales): ?>
                            <?php $pago = explode('_', $permisos_actuales); ?>
                            <tr>
                                <td>Permiso Circulación</td>
                                <td>El pago corresponde a <?= format_tipo_cargo($pago[2]) ?> de la PPU <?= $pago[1] ?> del año <?= $pago[3] ?></td>
                                <td class="text-end">$<?= format_clp($pago[0]) ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($monto_multa): ?>
                            <?php if ($monto_multa > 0): ?>
                                <tr>
                                    <td>Permiso Circulación</td>
                                    <td>El pago corresponde a Multa de Tránsito</td>
                                    <td class="text-end">$<?= format_clp($monto_multa) ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                        <tr>
                            <td colspan="2" class="text-end"><b>TOTAL A PAGAR</b></td>
                            <td class="text-end">$<?= format_clp($total) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-10 offset-md-1 d-flex justify-content-between">
            <a href="<?= route('permisocirculacion.index') ?>" class="btn btn-secondary">CANCELAR</a>
            <a href="<?= $tbk_url ?>" class="btn btn-success">PAGAR</a>
        </div>
    </div>
</div>  
@endsection