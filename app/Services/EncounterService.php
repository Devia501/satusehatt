<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EncounterService
{
    public function createEncounter(
        $token,
        $patientId,
        $patientName,
        $practitionerId,
        $practitionerName,
        $locationId
    ) {

        $timestamp = now()->utc()->format('Y-m-d\TH:i:s+00:00');

        $payload = [
            "resourceType" => "Encounter",

            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/encounter/" . env('SATUSEHAT_ORG_ID'),
                    "value" => "REG-" . strtoupper(Str::random(8))
                ]
            ],

            "status" => "arrived",

            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB",
                "display" => "ambulatory"
            ],

            "subject" => [
                "reference" => "Patient/{$patientId}",
                "display" => $patientName
            ],

            "participant" => [
                [
                    "type" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code" => "ATND",
                                    "display" => "attender"
                                ]
                            ]
                        ]
                    ],
                    "individual" => [
                        "reference" => "Practitioner/{$practitionerId}",
                        "display" => $practitionerName
                    ]
                ]
            ],

            "period" => [
                "start" => $timestamp
            ],

            "location" => [
                [
                    "location" => [
                        "reference" => "Location/{$locationId}",
                        "display" => "Ruang Poli Umum"
                    ]
                ]
            ],

            "statusHistory" => [
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => $timestamp
                    ]
                ]
            ],

            "serviceProvider" => [
                "reference" => "Organization/" . env('SATUSEHAT_ORG_ID')
            ]
        ];

        $response = Http::withToken($token)
            ->post(
                env('SATUSEHAT_BASE_FHIR') . '/Encounter',
                $payload
            );

        if (!$response->successful()) {
            throw new \Exception($response->body());
        }

        return $response->json();
    }
}