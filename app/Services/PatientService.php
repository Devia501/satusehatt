<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PatientService
{
    public function getPatient($token, $nik)
    {
        $response = Http::withToken($token)
            ->get(
                env('SATUSEHAT_BASE_FHIR') . '/Patient',
                [
                    'identifier' => "https://fhir.kemkes.go.id/id/nik|{$nik}"
                ]
            );

        if (!$response->successful()) {
            throw new \Exception('Pasien tidak ditemukan');
        }

        return $response->json();
    }
}