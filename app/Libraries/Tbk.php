<?php

namespace App\Libraries;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Tbk
{
    private int $tbk_app_id;
    private string $tbk_url;

    function __construct()
    {
        $this->tbk_app_id = config('envar.tbk_app_id');
        $this->tbk_url = rtrim(config('envar.tbk_url'), '/') . '/';
    }

    public function crear(string $buy_order, int $amount): array
    {
        $data = [
            'aplicacionId' => $this->tbk_app_id,
            'buyOrder' => $buy_order,
            'amount' => $amount,
            'callbackUrl' => route('permisocirculacion.validarpago'),
            'anularUrl' => route('home')
        ];

        try {
            $response  = Http::withOptions([
                'base_uri' => $this->tbk_url,
                'timeout' => 20,
                'verify' => false
            ])->post('create', $data);

            $response->throw();

            return [
                'response' => $response->body(),
                'statusCode' => $response->status(),
                'isOK' => $response->successful()
            ];
        } catch (RequestException $e) {
            Log::error("Tbk::crear Error HTTP: {$e->response->status()}: {$e->getMessage()}");
            return $this->handleError($e, $e->response->status());
        } catch (\Throwable $e) {
            Log::critical("Tbk::crear Error de Conexión: {$e->getMessage()}");
            return $this->handleError($e);
        }
    }

    public function estadoPorSessionId(string $session_id): array
    {
        try {
            $response  = Http::withOptions([
                'base_uri' => $this->tbk_url,
                'timeout' => 20,
                'verify' => false
            ])->get('comprobante', [
                'session_id' => $session_id
            ]);

            $response->throw();

            return [
                'response' => $response->body(),
                'statusCode' => $response->status(),
                'isOK' => $response->successful()
            ];
        } catch (RequestException $e) {
            Log::error("Tbk::crear Error HTTP: {$e->response->status()}: {$e->getMessage()}");
            return $this->handleError($e, $e->response->status());
        } catch (\Exception $e) {
            Log::critical("Tbk::estadoPorSessionId Error de Conexión: {$e->getMessage()}");
            return $this->handleError($e);
        }
    }

    public function estadoPortoken(string $token): array
    {
        try {
            $response  = Http::withOptions([
                'base_uri' => $this->tbk_url,
                'timeout' => 20,
                'verify' => false
            ])->get('status', [
                'aplicacionId' => $this->tbk_app_id,
                'token' => $token
            ]);

            $response->throw();

            return [
                'response' => $response->body(),
                'statusCode' => $response->status(),
                'isOK' => true
            ];
        } catch (RequestException $e) {
            Log::error("Tbk::estadoPortoken Error HTTP: {$e->response->status()}: {$e->getMessage()}");
            return $this->handleError($e, $e->response->status());
        } catch (\Throwable $e) {
            Log::critical("Tbk::estadoPortoken Error de Conexión: {$e->getMessage()}");
            return $this->handleError($e);
        }
    }

    private function handleError(\Throwable $e, int $statusCode = 500): array
    {
        return [
            'response' => json_encode([
                'error' => 'Error al consumir API TBK',
                'message' => $e->getMessage()
            ]),
            'statusCode' => $statusCode,
            'isOK' => false
        ];
    }
}