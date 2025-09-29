<?php

namespace App\Libraries;

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
            'callbackUrl' => route('webpay_comprobante'),
            'anularUrl' => route('home')
        ];

        try {
            $response  = Http::withOptions([
                'base_uri' => $this->tbk_url,
                'timeout' => 20,
                'verify' => false
            ])->post('create', $data);

            return [
                'response' => $response->body(),
                'statusCode' => $response->status(),
                'isOK' => $response->successful()
            ];
        } catch (\Throwable $e) {
            Log::warning("Tbk::crear error: {$e->getMessage()}");
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

            return [
                'response' => $response->body(),
                'statusCode' => $response->status(),
                'isOK' => $response->successful()
            ];
        } catch (\Exception $e) {
            Log::warning("Tbk::estadoPorSessionId error: {$e->getMessage()}");
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

            return [
                'response' => $response->body(),
                'statusCode' => $response->status(),
                'isOK' => true
            ];
        } catch (\Throwable $e) {
            Log::warning("Tbk::estadoPortoken error: {$e->getMessage()}");
            return $this->handleError($e);
        }
    }

    private function handleError(\Throwable $e): array
    {
        return [
            'response' => json_encode([
                'error' => 'Error al consumir API TBK',
                'message' => $e->getMessage()
            ]),
            'statusCode' => $e->getCode() ?: 500,
            'isOK' => false
        ];
    }
}