<?php

namespace App\Services\Rfx\OcrAnalysis\CheckOntologyHealth;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(): array
    {
        // OCR API URL 환경변수 검증
        $ocrBaseUrl = env('OCR_API_BASE_URL');
        if (!$ocrBaseUrl) {
            throw new \RuntimeException('OCR_API_BASE_URL 환경변수가 설정되지 않았습니다.');
        }

        try {
            $response = Http::get($ocrBaseUrl . '/analysis/ontology-health');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Ontology Health API 호출 실패: ' . $response->status()
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
