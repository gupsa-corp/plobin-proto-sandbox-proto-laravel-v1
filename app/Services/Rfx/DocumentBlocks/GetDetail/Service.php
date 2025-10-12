<?php

namespace App\Services\Rfx\DocumentBlocks\GetDetail;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(string $documentId, int $blockId, int $page = 1): array
    {
        // OCR-Reader API 호출
        $ocrBaseUrl = config('services.ocr.base_url', 'http://127.0.0.1:8000');
        $requestId = $documentId; // documentId를 requestId로 직접 사용

        try {
            $response = Http::timeout(30)
                ->get("{$ocrBaseUrl}/requests/{$requestId}/pages/{$page}/blocks/{$blockId}");

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => '블록을 찾을 수 없습니다',
                    'data' => null
                ];
            }

            $blockData = $response->json();

            // 문서 이름 조회 (request 메타데이터에서)
            $metaResponse = Http::timeout(30)->get("{$ocrBaseUrl}/requests/{$requestId}");
            $documentName = 'Unknown';
            if ($metaResponse->successful()) {
                $metaData = $metaResponse->json();
                $documentName = $metaData['filename'] ?? 'Unknown';
            }

            return [
                'success' => true,
                'message' => '블록 상세 정보를 성공적으로 조회했습니다',
                'data' => [
                    'document_id' => $documentId,
                    'document_name' => $documentName,
                    'page_number' => $page,
                    'block' => [
                        'block_id' => $blockData['block_id'] ?? $blockId,
                        'block_type' => $blockData['block_type'] ?? 'other',
                        'text' => $blockData['text'] ?? '',
                        'confidence' => $blockData['confidence'] ?? 0,
                        'bounding_box' => $blockData['bounding_box'] ?? [],
                        'position' => [
                            'x' => $blockData['bbox'][0] ?? 0,
                            'y' => $blockData['bbox'][1] ?? 0,
                            'width' => ($blockData['bbox'][2] ?? 0) - ($blockData['bbox'][0] ?? 0),
                            'height' => ($blockData['bbox'][3] ?? 0) - ($blockData['bbox'][1] ?? 0),
                        ],
                        'metadata' => $blockData['metadata'] ?? [],
                        'image_url' => "/api/rfx/documents/{$documentId}/blocks/{$blockId}/image?page={$page}"
                    ]
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
