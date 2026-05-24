<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\PatientService;
use App\Services\PractitionerService;
use App\Services\LocationService;
use App\Services\EncounterService;

class EncounterController extends Controller
{
    public function store(
        AuthService $authService,
        PatientService $patientService,
        PractitionerService $practitionerService,
        LocationService $locationService,
        EncounterService $encounterService
    ) {

        $token = $authService->getToken();

        $patient = $patientService->getPatient(
            $token,
            '9271060312000001'
        );

        $practitioner = $practitionerService->getPractitioner(
            $token,
            '7209061211900001'
        );

        $patientResource = $patient['entry'][0]['resource'];

        $practitionerResource = $practitioner['entry'][0]['resource'];

        $location = $locationService->createLocation($token);

        $encounter = $encounterService->createEncounter(
            $token,
            $patientResource['id'],
            $patientResource['name'][0]['text'] ?? 'Patient',
            $practitionerResource['id'],
            $practitionerResource['name'][0]['text'] ?? 'Doctor',
            $location['id']
        );

        return response()->json([
            'success' => true,
            'data' => $encounter
        ]);
    }
}