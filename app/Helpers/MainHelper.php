<?php

// GLOBAL

if (!function_exists('separar_rut')) {
    function separar_rut(string $rut): array {
        return explode('-', $rut);
    }
}

if (!function_exists('obtenerNuevoPagoId')) {
    function obtenerNuevoPagoId(string $tipo, string|null $pago_id_actual): string {
        $anio_actual = date('Y');
        $prefijo = $tipo . $anio_actual;
        $nuevo_correlativo = 1;

        if (!is_null($pago_id_actual)) {
            $ultimo_correlativo = substr($pago_id_actual, -8);
            $nuevo_correlativo = (int) $ultimo_correlativo + 1;
        }

        $completar_correlativo = str_pad($nuevo_correlativo, 8, '0', STR_PAD_LEFT);
        return $prefijo . $completar_correlativo;
    }
}

// PERMISO CIRCULACION

if (!function_exists('formatear_longitud_multa')) {
    function formatear_longitud_multa(string $multa_id) {
        $length_min = 8;
        $length_dif = $length_min - strlen($multa_id);
        $ceros = '';

        for ($i = 0; $i < $length_dif; $i++) {
            $ceros .= '0';
        }

        $multa = $ceros.$multa_id;

        return $multa;
    }
}

if(!function_exists('formatear_longitud_placa')) {
    function formatear_longitud_placa($patente) {
        $patente = trim($patente);
        preg_match('/^([a-zA-Z]+)([0-9]+)$/', $patente, $arreglo);
        if(strlen($patente)==5){
            $patente = strtoupper($arreglo[1])."0".$arreglo[2];
        }
        if(strlen($patente)==4){
            $patente = strtoupper($arreglo[1])."00".$arreglo[2];
        }
        return "$patente";
    }
}

if (!function_exists('calcular_multa_transito')) {
    function calcular_multa_transito($anio, $nivel, $utm, $valor) {
        $total = 0;
        $valor = floatval($valor);
        $utm = floatval($utm);
        $arancel = 4570;

        if ($nivel == 1 && $anio <= 2007) {
            //$arancel = $valor * 0.08;
            $total = round($arancel + $valor);
        } else if ($nivel == 1 && $anio >= 2008) {
            $total = round($arancel + $valor);
        } else if ($nivel == 2) {
            if ($anio <= 2007) {
                $calculo = round($utm * $valor);
                $total = round($arancel + $calculo);
            } else if($anio >= 2008) {
                $calculo = round($utm * $valor);
                $total = round($arancel + $calculo);
            }
        }
        return $total;
    }
}

if (!function_exists('calcular_multa_transito_arancel_anterior')) {
    function calcular_multa_transito_arancel_anterior($anio, $nivel, $utm, $valor) {
        $total = 0;
        $valor = floatval($valor);
        $utm = floatval($utm);
        $arancel = 4570;

        if ($nivel == 1 && $anio <= 2007) {
            //$arancel = $valor * 0.08;
            $total = round($arancel + $valor);
        } else if ($nivel == 1 && $anio >= 2008) {
            $total = round($arancel + $valor);
        } else if ($nivel == 2) {
            if ($anio <= 2007) {
                $calculo = round($utm * $valor);
                $total = round($arancel + $calculo);
            } else if($anio >= 2008) {
                $calculo = round($utm * $valor);
                $total = round($arancel + $calculo);
            }
        }
        return $total;
    }
}

if (!function_exists('format_tipo_cargo'))  {
    function format_tipo_cargo($tipo_cargo)  {
        switch ((int)$tipo_cargo) {
            case 0: return 'Total';
            case 1: return 'Primera Cuota';
            case 2: return 'Segunda Cuota';
            default: return 'Sin datos';
        }
    }
}

if (!function_exists('format_clp')) {
    function format_clp($number) {
        return number_format($number, 0, '', '.');  
    }
}

if (!function_exists('format_date')) {
    function format_date($date, $muestra_tiempo = true, $del = '/') {
        $year   = substr($date, 0, 4);
        $month  = substr($date, 4, 2);
        $day    = substr($date, 6, 2);

        $time = "";

        if(strlen($date) > 9 && strlen($date) % 2 === 0) {
            $hour   = substr($date, 8, 2);
            $min    = substr($date, 10, 2);
            $seg    = substr($date, 12, 2);
            $time   = " $hour:$min:$seg";
        }

        if($muestra_tiempo == false) {
            $time = "";
        }

        return "$day$del$month$del$year$time";
    }
}

if (!function_exists('formatDate')) {
    function formatDate($fecha, $fmt="d/m/Y") {
        $ano = substr($fecha, 0, 4);
        $mes = substr($fecha, 4, 2);
        $dia = substr($fecha, 6, 2);

        $tmp = mktime(0, 0, 0, $mes, $dia, $ano);
        return date($fmt, $tmp);
    }
}

if (!function_exists('format_payment_code'))  {
    function format_payment_code($code)  {
        switch ($code) {
            case 'VD': return 'Venta Debito';
            case 'VP': return 'Venta Prepago';
            case 'VN': return 'Venta Normal';
            case 'SI': return '3 cuotas sin interés';
            case 'S2': return '2 cuotas sin interés';
            case 'NC': return 'N cuotas sin interés';
            case 'VC': return 'Venta en cuotas';
            default: return 'Sin Información';
        }
    }
}

if (!function_exists('formatear_zona_horaria')) {
    function formatear_zona_horaria($fecha_utc, $datetimezone = 'America/Santiago') {
        try {
            $datetime = new DateTime($fecha_utc);
        } catch (\Exception $e) {
            return $fecha_utc;
        }
        $timezone = $datetime->getTimezone();
        $is_utc = false;
        if ($timezone->getName() === 'UTC' || $timezone->getName() === 'Z' || $timezone->getOffset($datetime) === 0) {
            $is_utc = true;
        }

        if ($is_utc) {
            try {
                $timezone_destino = new DateTimeZone($datetimezone);
                $datetime->setTimezone($timezone_destino);
                return $datetime->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return $fecha_utc;
            }
        } else {
            return $fecha_utc;
        }
    }   
}

if(!function_exists('calcular_fecha')) {
    function calcular_fecha() {
        $fecha_actual = date_create(date('Ymd'));
        $fecha_formateada = date_format($fecha_actual, 'Y-m-d');
        $fecha_restada = date('Y-m-d',strtotime($fecha_formateada.'- 5 days'));
        $fecha = str_replace('-', '', $fecha_restada);
        return $fecha;
    }
}

if(!function_exists('pdf_text')) {
    function pdf_text(string $texto) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
    }
}

// PATENTES MUNICIPALES

if(!function_exists('parseAnioToSemestre')) {
    function parseAnioToSemestre(int $anio, int $cuota) {
        return $cuota == 2 ? ($anio + 1) : $anio;
    }
}

if(!function_exists('parseCuotaToSemestre')) {
    function parseCuotaToSemestre(int $cuota) {
        return $cuota == 2 ? 1 : 2;
    }
}

if(!function_exists('vencimiento')) {
    function vencimiento(int $anio, int $cuota, int $tipo=1) {
        // tipo=>1=>cuota; tipo=>2=>semestre
        if ($tipo == 2) {
            return $cuota == 2 ? '31/07/'.$anio : '31/01/'.$anio;
        } else {
            return $cuota == 2 ? '31/01/'.($anio + 1) : '31/07/'.$anio;
        }
    }
}

if(!function_exists('periodo')) {
    function periodo(int $anio, int $cuota, int $tipo=1) {
        // tipo=>1=>cuota; tipo=>2=>semestre
        if ($tipo == 2) {
            return $cuota == 2 ? '31/07/'.$anio : '31/01/'.$anio;
        } else {
            return $cuota == 2 ? '31/01/'.($anio + 1) : '31/07/'.$anio;
        }
    }
}
