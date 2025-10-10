<?php

namespace App\Services\Rfx\FileUpload\DeleteOcrRequest;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute(array $data): array
    {
        try {
            $requestId = $data['request_id'];

            $response = Http::delete(config('services.ocr.base_url') . "/requests/{$requestId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'OCR 요청이 성공적으로 삭제되었습니다.',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'OCR 요청 삭제에 실패했습니다.',
                'data' => null
            ];

        } catch (\Exception $e) {
            Log::error('OCR 요청 삭제 실패: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'OCR 요청 삭제 중 오류가 발생했습니다.',
                'data' => null
            ];
        }
    }
}
