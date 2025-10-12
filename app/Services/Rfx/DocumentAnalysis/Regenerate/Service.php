<?php

namespace App\Services\Rfx\DocumentAnalysis\Regenerate;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute($documentId): array
    {
        try {
            // OCR 서비스에 재분석 요청
            $response = Http::post(config('services.ocr.base_url') . "/requests/{$documentId}/reanalyze");

            if (!$response->successful()) {
                Log::error('OCR API 재분석 요청 실패', [
                    'documentId' => $documentId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'OCR 서비스 재분석 요청에 실패했습니다.'
                ];
            }

            Log::info('OCR API 재분석 요청 성공', [
                'documentId' => $documentId
            ]);

            return [
                'success' => true,
                'message' => '문서 재분석을 시작했습니다. 분석이 완료되면 결과가 업데이트됩니다.'
            ];

        } catch (\Exception $e) {
            Log::error('OCR API 재분석 요청 실패 (Exception)', [
                'documentId' => $documentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => '재분석 요청 중 오류가 발생했습니다: ' . $e->getMessage()
            ];
        }
    }
}