<?php

namespace App\Services\Rfx\FileUpload\GetOcrRequests;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute(array $data): array
    {
        try {
            $page = $data['page'] ?? 1;
            $limit = $data['limit'] ?? 10;

            $response = Http::get(config('services.ocr.base_url') . '/requests', [
                'page' => $page,
                'limit' => $limit
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'OCR 요청 목록을 성공적으로 조회했습니다.',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'OCR 요청 목록 조회에 실패했습니다.',
                'data' => null
            ];

        } catch (\Exception $e) {
            Log::error('OCR 요청 목록 조회 실패: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'OCR 요청 목록 조회 중 오류가 발생했습니다.',
                'data' => null
            ];
        }
    }
}
