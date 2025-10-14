<?php

namespace App\Services\Rfx\BlockSummary\GenerateSummary;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(array $params): array
    {
        $documentId = $params['document_id'];
        $blocks = $params['blocks'];

        if (empty($blocks)) {
            return [
                'success' => false,
                'message' => '요약할 블록이 없습니다',
                'data' => null
            ];
        }

        try {
            // 블록 텍스트 수집
            $texts = array_map(function ($block) {
                return $block['text'] ?? '';
            }, $blocks);

            // 전체 텍스트 결합
            $fullText = implode("\n", array_filter($texts));

            // AI 요약 API 호출
            $summaryApiUrl = config('services.summary.base_url', 'http://seoul.gupsa.net:7576');

            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$summaryApiUrl}/api/summarize", [
                    'text' => $fullText,
                    'document_id' => $documentId,
                    'block_count' => count($blocks),
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'AI 요약 API 호출에 실패했습니다',
                    'data' => null
                ];
            }

            $summaryData = $response->json();

            return [
                'success' => true,
                'message' => '요약이 성공적으로 생성되었습니다',
                'data' => [
                    'summary' => $summaryData['summary'] ?? '요약을 생성할 수 없습니다',
                    'key_points' => $summaryData['key_points'] ?? [],
                    'generated_at' => now()->toDateTimeString(),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'AI 요약 생성 중 오류가 발생했습니다: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
