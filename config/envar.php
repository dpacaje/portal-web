<?php

return [
    // app
    'app_tipo' => env('APP_TIPO'),
    'app_municipalidad' => env('APP_MUNICIPALIDAD'),
    // tbk
    'tbk_app_id' => env('TBK_APP_ID'),
    'tbk_url' => env('TBK_URL'),
    'prefijo_oc' => env('TBK_PREFIJO_OC'),
    // ecert
    'ecert_url_post' => env('ECERT_URL_POST'),
    'ecert_url_get' => env('ECERT_URL_GET'),
    'ecert_user' => env('ECERT_USER'),
    'ecert_pass' => env('ECERT_PASS'),
    // mail
    'mail_address' => env('MAIL_FROM_ADDRESS'),
    // tesoreria
    'teso_enable' => env('TESO_ENABLE'),
    'teso_url' => env('TESO_URL'),
    // soap
    'soap_enable' => env('SOAP_ENABLE'),
    'soap_url' => env('SOAP_URL'),
    'soap_user' => env('SOAP_USER'),
    'soap_pass' => env('SOAP_PASS'),
    'soap_id_muni' => env('SOAP_ID_MUNI'),
    // mtt
    'mtt_enable' => env('MTT_ENABLE'),
    'mtt_url' => env('MTT_URL'),
    'mtt_uri' => env('MTT_URI'),
    'mtt_user' => env('MTT_USER'),
    'mtt_pass' => env('MTT_PASS'),
];