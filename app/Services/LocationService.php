<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LocationService
{
    public function createLocation($token)
    {
        $payload = [
            "resourceType" => "Location",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/location/" . env('SATUSEHAT_ORG_ID'),
                    "value" => "SS-UKP-POLI-" . strtoupper(Str::random(6))
                ]
            ],
            "status" => "active",
            "name" => "Ruang Poli Umum",
            "description" => "Ruang Poli Umum",
            "mode" => "instance",
            "physicalType" => [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/location-physical-type",
                        "code" => "ro",
                        "display" => "Room"
                    ]
                ]
            ],
            "managingOrganization" => [
                "reference" => "Organization/" . env('SATUSEHAT_ORG_ID')
            ]
        ];

        $response = Http::withToken($token)
            ->post(
                env('SATUSEHAT_BASE_FHIR') . '/Location',
                $payload
            );

        if (!$response->successful()) {
            throw new \Exception($response->body());
        }

        return $response->json();
    }
}