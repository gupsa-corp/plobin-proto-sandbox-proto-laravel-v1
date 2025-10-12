<?php

namespace App\Services\Rfx\DocumentBlocks\Update;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(string $documentId, int $blockId, array $updateData, int $page = 1): array
    {
        // OCR-Reader API 호출
        $ocrBaseUrl = config('services.ocr.base_url', 'http://127.0.0.1:8000');
        $requestId = $documentId; // documentId를 requestId로 직접 사용

        try {
            $response = Http::timeout(30)
                ->put("{$ocrBaseUrl}/requests/{$requestId}/pages/{$page}/blocks/{$blockId}", $updateData);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => '블록 수정에 실패했습니다',
                    'data' => null
                ];
            }

            $result = $response->json();

            return [
                'success' => true,
                'message' => '블록이 성공적으로 수정되었습니다',
                'data' => [
                    'document_id' => $documentId,
                    'page_number' => $page,
                    'block_id' => $blockId,
                    'updated_block' => $result['block'] ?? []
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'OCR API 호출 중 오류가 발생했습니다: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
