<?php

namespace App\Services\Rfx\BlockSummary\GetBlocks;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(string $documentId): array
    {
        $ocrBaseUrl = config('services.ocr.base_url', 'http://127.0.0.1:8000');

        try {
            // 문서 메타데이터 조회 (총 페이지 수 확인)
            $metaResponse = Http::timeout(30)->get("{$ocrBaseUrl}/requests/{$documentId}");

            if (!$metaResponse->successful()) {
                return [
                    'success' => false,
                    'message' => '문서 정보를 가져올 수 없습니다',
                    'data' => null
                ];
            }

            $metaData = $metaResponse->json();
            $totalPages = $metaData['total_pages'] ?? 1;

            // 모든 페이지의 블록 수집
            $allBlocks = [];

            for ($page = 1; $page <= $totalPages; $page++) {
                $response = Http::timeout(30)
                    ->get("{$ocrBaseUrl}/requests/{$documentId}/pages/{$page}/blocks", [
                        'start' => 0,
                        'limit' => 1000, // 한 페이지당 최대 1000개 블록
                    ]);

                if ($response->successful()) {
                    $pageData = $response->json();
                    $blocks = $pageData['blocks'] ?? [];

                    // 페이지 번호 추가
                    foreach ($blocks as &$block) {
                        $block['page_number'] = $page;
                    }

                    $allBlocks = array_merge($allBlocks, $blocks);
                }
            }

            return [
                'success' => true,
                'message' => '블록 데이터를 성공적으로 수집했습니다',
                'data' => [
                    'blocks' => $allBlocks,
                    'total_pages' => $totalPages,
                    'total_blocks' => count($allBlocks),
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
