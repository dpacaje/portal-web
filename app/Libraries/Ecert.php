<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Log;

class Ecert
{
    private string $ecert_url_post;
    private string $ecert_url_get;
    private string $ecert_user;
    private string $ecert_pass;
    private string $ecert_rut_firmante;
    private string $ecert_codigo_bin;

    public function __construct()
    {
        $this->ecert_url_post = env('ECERT_URL_POST');
        $this->ecert_url_get = env('ECERT_URL_GET');
        $this->ecert_user = env('ECERT_USER');
        $this->ecert_pass = env('ECERT_PASS');
        $this->ecert_rut_firmante = env('ECERT_RUT_FIRMANTE');
        $this->ecert_codigo_bin = env('ECERT_CODIGO_BIN');
    }

	function firmar($base_64_pdf, $guid=null)
	{
        try {
            if (is_null($guid)) {
                $guid_result = $this->get_guid();
    
                if(is_null($guid_result['guid'])){
                    $data['estado'] = false;
                    // $data['descripcion'] =  $firma->Cabecera->Resultado->Descripcion;
                    // $data['detalle'] = $firma->Cabecera->Resultado->Detalle;
                    // $data['msg'] =  'No se ha podido obtener el guid.';
                    return $data;
                }
    
                $guid = $guid_result['guid'];
            }
    
            $firma = $this->enviar_pdf($base_64_pdf, $guid);
            if($firma->Cabecera->Resultado->Estado == 'true')://Intento de firma exitoso
                if($firma->Documentos->DatosDocumento->Estado == true)://Tenemos documento firmado
                    if(!empty((array)$firma->Documentos->DatosDocumento->Guid))://Tenemos un GUID, entonces podemos construir la URL
                        $data['estado'] = true;
                        $data['descripcion'] = 'Comprobante generado.';
                        $data['url'] = $this->ecert_url_get . $firma->Documentos->DatosDocumento->Guid;
                    else://No recibimos GUID, no podemos construir la URL
                        $data['estado'] = false;
                        $data['descripcion'] = $firma->Cabecera->Resultado->Descripcion;
                        $data['detalle'] = $firma->Cabecera->Resultado->Detalle;
                        // $data['msg'] = 'No se ha obtenido el GUID, no podemos construir la URL.';
                    endif;
                endif;
            else://Ha ocurrido un error al intentar firmar
                $data['estado'] = false;
                $data['descripcion'] = $firma->Cabecera->Resultado->Descripcion;
                $data['detalle'] = $firma->Cabecera->Resultado->Detalle;
                // $data['msg'] = 'No se ha podido firmar el documento.';
            endif;
            return $data;
        } catch (\Exception $e) {
            Log::error('Libraries/Ecert/firmar: Error interno: ' . $e->getMessage());
            $data['estado'] = false;
            return $data;
        }
	}

	function enviar_pdf($base_64_pdf, $guid)
	{
        try {
            $sobre_xml =
            "<Sobre>
                <Cabecera>
                    <Firmantes>
                        <DatosFirmante>
                            <Rut>$this->ecert_rut_firmante</Rut>
                        </DatosFirmante>
                    </Firmantes>
                </Cabecera>
                <Documentos>
                    <DatosDocumento tipo=\"PDF\" Codigo=\"$this->ecert_codigo_bin\">
                        <Contenido>
                            $base_64_pdf
                        </Contenido>
                        <CodigoGUID>$guid</CodigoGUID>
                        <PosIzq>1500</PosIzq>
                        <PosInf>1500</PosInf>
                        <PosAncho>1500</PosAncho>
                        <PosLargo>1500</PosLargo>
                    </DatosDocumento>
                </Documentos>
            </Sobre>";

            $soapclient = new \SoapClient($this->ecert_url_post, ['trace' => true, 'exceptions' => true]);

            $params = array(
                'Usuario' => $this->ecert_user,
                'Clave' => $this->ecert_pass,
                'SobreXML' => $sobre_xml
            );

            $response = $soapclient->FirmaDocumento($params);

            $xml = simplexml_load_string($response->FirmaDocumentoResult);

            return $xml;
        } catch (\SoapFault $e) {
            Log::error('Libraries/Ecert/enviar_pdf: Error SOAP: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Libraries/Ecert/enviar_pdf: Error interno: ' . $e->getMessage());
            return null;
        }
	}

    function get_guid()
    {
        try {
            $soapclient = new \SoapClient($this->ecert_url_post, ['trace' => true, 'exceptions' => true]);

            $params = array(
                'Usuario' => $this->ecert_user,
                'Clave' => $this->ecert_pass
            );

            $response = $soapclient->SolicitarGuid($params);

            $xml = simplexml_load_string($response->SolicitarGuidResult);

            $urlResponse = $xml->Cabecera->Resultado->Url;
            $guidResponse = $xml->Cabecera->Resultado->Guid;

            if (strlen($guidResponse) < 10 || strlen($urlResponse) < 10) {
                return null;
            } else {
                return [
                    "url" => $urlResponse,
                    "guid" => $guidResponse
                ]; 
            }
        } catch (\SoapFault $e) {
            Log::error('Libraries/Ecert/get_guid: Error SOAP: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Libraries/Ecert/get_guid: Error interno: ' . $e->getMessage());
            return null;
        }
    }
}