<?php

namespace App\Services\Rfx\OcrAnalysis\GetAssetTypes;

use Illuminate\Support\Facades\Http;

class Service
{
    private const API_BASE_URL = 'http://localhost:6003';

    public function execute(): array
    {
        try {
            $response = Http::get(self::API_BASE_URL . '/analysis/asset-types');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Asset Types API 호출 실패: ' . $response->status()
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'data' => $data
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
