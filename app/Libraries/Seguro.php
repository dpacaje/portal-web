<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Log;

class Seguro
{
    private $soap_url;
    private $soap_user;
    private $soap_pass;
    private $soap_id_municipalidad;

    public function __construct()
    {
        $this->soap_url = config('envar.soap_url');
        $this->soap_user = config('envar.soap_user');
        $this->soap_pass = config('envar.soap_pass');
        $this->soap_id_municipalidad = config('envar.soap_id_muni');
    }

    public function getSoap($placa)
    {
        $placa = $this->formatearPlaca($placa);

        $params = array(
            'NroPoliza' => '',
            'Patente' => $placa,
            'Usuario' => $this->soap_user,
            'Clave' => $this->soap_pass,
            'Municipalidad' => $this->soap_id_municipalidad
        );

        try {
            $soapclient = new \SoapClient($this->soap_url, [
                'connection_timeout' => 5,
                'exceptions' => true,
                'trace' => true,
                'stream_context' => stream_context_create([
                    'http' => ['timeout' => 10]
                ])
            ]);
            $response = $soapclient->Consulta_eSOAP($params);
            $xml = simplexml_load_string($response->Consulta_eSOAPResult->any);
            return $this->procesarRespuesta($xml);
        } catch (\SoapFault $e) {
            Log::error('Libraries/Seguro/getSoap: Error SOAP: ' . $e->getMessage());
            $data['status'] = false;
            $data['code'] = 503;
            $data['glosa'] = 'Error al consultar el servicio: ' . $e->getMessage();
            return $data;
        } catch (\Exception $e) {
            Log::error('Libraries/Seguro/getSoap: Error interno: ' . $e->getMessage());
            $data['status'] = false;
            $data['code'] = 504;
            $data['glosa'] = 'Error inesperado: ' . $e->getMessage();
            return $data;
        }
    }

    private function formatearPlaca(string $placa): string
    {
        preg_match('/^([a-zA-Z]+)([0-9]+)$/', $placa, $arreglo);
        return match (strlen($placa)) {
            6 => strtoupper($arreglo[1]) . ' ' . $arreglo[2],
            5 => strtoupper($arreglo[1]) . ' 0' . $arreglo[2],
            default => strtoupper($placa)
        };
    }

    private function procesarRespuesta(\SimpleXMLElement $response): array
    {
        $ns = $response->NewDataSet;
        if ($ns->STATUS) {
            $xml = $ns->STATUS;
            if ((string)$xml->Estado === '00') {
                return [
                    'status' => true,
                    'code' => (string) $xml->Estado,
                    'glosa' => (string) $xml->Glosa,
                    'RutCia' => (string) $xml->RutCia,
                    'NombreCompania' => (string) $xml->NombreCompania,
                    'NroPoliza' => (string) $xml->NroPoliza,
                    'FechaInicio' => (string) $xml->FechaInicio,
                    'FechaTermino' => (string) $xml->FechaTermino,
                ];
            }
        } elseif ($ns->ESOAP) {
            $xml = $ns->ESOAP;
            return [
                'status' => false,
                'code' => (string) $xml->Estado,
                'glosa' => (string) $xml->Glosa,
            ];
        }

        return [
            'status' => false,
            'code' => 999,
            'glosa' => 'Respuesta inesperada del WebService'
        ];
    }
}
