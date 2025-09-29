<?php

namespace App\Libraries;

use SoapClient;
use SoapParam;
use SimpleXMLElement;
use Illuminate\Support\Facades\Log;

class Revision
{
    private string $url;
    private string $uri;
    private string $user;
    private string $pass;

    public function __construct()
    {
        $this->url = config('envar.mtt_url');
        $this->uri = config('envar.mtt_uri');
        $this->user = config('envar.mtt_user');
        $this->pass = config('envar.mtt_pass');
    }

    public function getRevision(string $placa): array
    {
        $placa = $this->formatearPlaca($placa);

        try {
            $soapClient = new SoapClient(null, [
                'location' => $this->url,
                'uri' => $this->uri,
                'trace' => true,
                'exceptions' => true,
                'use' => SOAP_ENCODED,
                'style' => SOAP_RPC,
                'connection_timeout' => 5,
                'stream_context' => stream_context_create([
                    'http' => ['timeout' => 10]
                ])
            ]);

            $soapClient->__soapCall('consultaRevisionTecnica', [
                new SoapParam($this->user, 'arg0'),
                new SoapParam($this->pass, 'arg1'),
                new SoapParam($placa, 'arg2'),
            ]);

            $xml = $this->extraerXmlDesdeRespuesta($soapClient->__getLastResponse());

            if (!isset($xml->VEHICULO->REVISION_TECNICA)) {
                return $this->respuesta(false, 404, 'El vehículo no registra revisión técnica');
            }

            return $this->respuesta(true, 200, 'Consulta exitosa', [
                'vehiculo' => $this->xmlToArray($xml->VEHICULO),
                'revision_tecnica' => $this->xmlToArray($xml->VEHICULO->REVISION_TECNICA),
                'revision_gases' => isset($xml->VEHICULO->REVISION_GASES)
                    ? $this->xmlToArray($xml->VEHICULO->REVISION_GASES)
                    : [],
                'consulta' => $this->xmlToArray($xml),
            ]);
        } catch (\SoapFault $e) {
            Log::error('Revision::getRevision | SOAP Fault: ' . $e->getMessage());
            return $this->respuesta(false, 503, 'Error al consultar el servicio: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Revision::getRevision | Error general: ' . $e->getMessage());
            return $this->respuesta(false, 504, 'Error inesperado: ' . $e->getMessage());
        }
    }

    private function formatearPlaca(string $placa): string
    {
        if (preg_match('/^([a-zA-Z]+)([0-9]+)$/', $placa, $match)) {
            return strlen($placa) === 5
                ? strtoupper($match[1]) . '0' . $match[2]
                : strtoupper($match[1]) . $match[2];
        }
        return strtoupper($placa);
    }

    private function extraerXmlDesdeRespuesta(string $soapXml): SimpleXMLElement
    {
        $xml = new SimpleXMLElement($soapXml);
        $body = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->Body
            ->children('http://revtec.ws.sgprt.mtt.cl/');
        $return = $body->consultaRevisionTecnicaResponse->children() ?? null;

        if (!$return) {
            throw new \Exception('Respuesta SOAP inválida: falta nodo <return>');
        }

        return new SimpleXMLElement((string) $return);
    }

    private function xmlToArray(SimpleXMLElement $xml): array
    {
        $output = [];
        foreach ($xml->attributes() as $key => $val) {
            $output[$key] = (string) $val;
        }
        return $output;
    }

    private function respuesta(bool $status, int $code, string $message, array $data = []): array
    {
        return [
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
}
