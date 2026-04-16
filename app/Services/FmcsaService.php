<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FmcsaService
{
    protected string $baseUrl = 'https://mobile.fmcsa.dot.gov/qc/services';

    public function checkAuthorityByDocket(string $docketNumber): array
    {
        $webKey = config('services.fmcsa.webkey');

        if (!$webKey) {
            return [
                'success' => false,
                'message' => 'FMCSA WebKey not configured'
            ];
        }

        $response = Http::timeout(30)->get(
            "{$this->baseUrl}/carriers/docket-number/{$docketNumber}",
            ['webKey' => $webKey]
        );

        if (!$response->ok()) {
            return [
                'success' => false,
                'message' => 'FMCSA API error',
                'status'  => $response->status()
            ];
        }

        $data = $response->json();

        if (empty($data['content'][0])) {
            return [
                'success' => false,
                'message' => 'No carrier found for this MC/FF/MX number'
            ];
        }

        $carrier   = $data['content'][0];
        $authority = $carrier['authority'] ?? [];

        return [
            'success'            => true,
            'docket_number'      => $docketNumber,
            'usdot_number'       => $carrier['usdotNumber'] ?? null,
            'legal_name'         => $carrier['legalName'] ?? null,
            'dba_name'           => $carrier['dbaName'] ?? null,
            'allowed_to_operate' => $authority['allowedToOperate'] ?? false,
            'authority_status'   => $authority['status'] ?? 'UNKNOWN',
            'authority_types'    => $authority['authorityType'] ?? [],
        ];
    }
}
