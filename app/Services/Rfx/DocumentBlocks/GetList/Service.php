<?php

namespace App\Services\Rfx\DocumentBlocks\GetList;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(array $params): array
    {
        $documentId = $params['document_id'];
        $page = $params['page'] ?? 1;
        $blockType = $params['block_type'] ?? null;
        $confidenceMin = $params['confidence_min'] ?? null;
        $limit = $params['limit'] ?? 20;
        $start = ($page - 1) * $limit;

        // OCR-Reader API 호출
        $ocrBaseUrl = config('services.ocr.base_url', 'http://127.0.0.1:8000');
        $requestId = $documentId; // documentId를 requestId로 직접 사용

        try {
            $queryParams = [
                'start' => $start,
                'limit' => $limit,
            ];

            if ($blockType) {
                $queryParams['block_type'] = $blockType;
            }

            if ($confidenceMin !== null) {
                $queryParams['confidence_min'] = $confidenceMin;
            }

            $response = Http::timeout(30)
                ->get("{$ocrBaseUrl}/requests/{$requestId}/pages/{$page}/blocks", $queryParams);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'OCR API 호출에 실패했습니다',
                    'data' => null
                ];
            }

            $data = $response->json();

            // 통계 정보 조회
            $statsResponse = Http::timeout(30)
                ->get("{$ocrBaseUrl}/requests/{$requestId}/pages/{$page}/blocks/stats");

            $statistics = $statsResponse->successful() ? $statsResponse->json() : [];

            // 문서 이름 조회 (request 메타데이터에서)
            $metaResponse = Http::timeout(30)->get("{$ocrBaseUrl}/requests/{$requestId}");
            $documentName = 'Unknown';
            if ($metaResponse->successful()) {
                $metaData = $metaResponse->json();
                $documentName = $metaData['filename'] ?? 'Unknown';
            }

            return [
                'success' => true,
                'message' => '블록 목록을 성공적으로 조회했습니다',
                'data' => [
                    'document_id' => $documentId,
                    'document_name' => $documentName,
                    'page_number' => $page,
                    'blocks' => $data['blocks'] ?? [],
                    'statistics' => [
                        'total_blocks' => $statistics['total_blocks'] ?? 0,
                        'by_type' => $statistics['by_type'] ?? [],
                        'average_confidence' => $statistics['average_confidence'] ?? 0
                    ],
                    'pagination' => [
                        'current_page' => $page,
                        'items_per_page' => $limit,
                        'total_items' => $data['total'] ?? 0,
                        'total_pages' => (int) ceil(($data['total'] ?? 0) / $limit)
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
