<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AuthService
{
    public function getToken()
    {
        $response = Http::asForm()->post(
            env('SATUSEHAT_BASE_AUTH') . '/accesstoken?grant_type=client_credentials',
            [
                'client_id' => env('SATUSEHAT_CLIENT_ID'),
                'client_secret' => env('SATUSEHAT_CLIENT_SECRET'),
            ]
        );

        if (!$response->successful()) {
            throw new \Exception('Gagal mendapatkan token');
        }

        return $response->json()['access_token'];
    }
}