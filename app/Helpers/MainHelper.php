<?php

if (!function_exists('separar_rut')) {
    function separar_rut(string $rut): array {
        return explode('-', $rut);
    }
}
