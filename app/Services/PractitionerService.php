<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PractitionerService
{
    public function getPractitioner($token, $nik)
    {
        $response = Http::withToken($token)
            ->get(
                env('SATUSEHAT_BASE_FHIR') . '/Practitioner',
                [
                    'identifier' => "https://fhir.kemkes.go.id/id/nik|{$nik}"
                ]
            );

        if (!$response->successful()) {
            throw new \Exception('Dokter tidak ditemukan');
        }

        return $response->json();
    }
}