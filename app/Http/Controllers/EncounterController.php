<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\PatientService;
use App\Services\PractitionerService;
use App\Services\LocationService;
use App\Services\EncounterService;
use Illuminate\Http\Request;

class EncounterController extends Controller
{
    public function store(
        Request $request,
        AuthService $authService,
        PatientService $patientService,
        PractitionerService $practitionerService,
        LocationService $locationService,
        EncounterService $encounterService
    ) {
        // Cek apakah credentials sudah diisi
        if (empty(env('SATUSEHAT_CLIENT_ID')) || empty(env('SATUSEHAT_CLIENT_SECRET'))) {
            return response()->json([
                'success' => false,
                'error_code' => 'MISSING_CREDENTIALS',
                'message' => 'Credentials SatuSehat belum diisi. Silakan isi Client ID, Client Secret, dan Org ID terlebih dahulu.'
            ], 422);
        }

        try {
            $token = $authService->getToken();

            $patient = $patientService->getPatient($token, '9271060312000001');
            $practitioner = $practitionerService->getPractitioner($token, '7209061211900001');

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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error_code' => 'API_ERROR',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saveConfig(Request $request)
    {
        $clientId     = trim($request->input('client_id', ''));
        $clientSecret = trim($request->input('client_secret', ''));
        $orgId        = trim($request->input('org_id', ''));

        if (empty($clientId) || empty($clientSecret) || empty($orgId)) {
            return response()->json([
                'success' => false,
                'message' => 'Semua field credentials wajib diisi.'
            ], 422);
        }

        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        $content = preg_replace('/^SATUSEHAT_CLIENT_ID=.*/m',     "SATUSEHAT_CLIENT_ID={$clientId}",     $content);
        $content = preg_replace('/^SATUSEHAT_CLIENT_SECRET=.*/m', "SATUSEHAT_CLIENT_SECRET={$clientSecret}", $content);
        $content = preg_replace('/^SATUSEHAT_ORG_ID=.*/m',        "SATUSEHAT_ORG_ID={$orgId}",           $content);

        file_put_contents($envPath, $content);

        // Clear config cache
        \Artisan::call('config:clear');

        return response()->json([
            'success' => true,
            'message' => 'Credentials berhasil disimpan.'
        ]);
    }
}