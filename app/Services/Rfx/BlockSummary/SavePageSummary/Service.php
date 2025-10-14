<?php

namespace App\Services\Rfx\BlockSummary\SavePageSummary;

use App\Models\Rfx\PageSummary;
use App\Services\Rfx\BlockSummary\GenerateSummary\Service as GenerateSummaryService;

class Service
{
    /**
     * 페이지별 요약 저장
     *
     * @param array $data [document_summary_id, page_number, blocks]
     * @return array [success, data/message]
     */
    public function execute(array $data): array
    {
        try {
            // 해당 페이지의 블록들로 AI 요약 생성
            $summaryService = new GenerateSummaryService();
            $summaryResult = $summaryService->execute([
                'document_id' => $data['document_id'] ?? null,
                'blocks' => $data['blocks'],
            ]);

            if (!$summaryResult['success']) {
                return $summaryResult;
            }

            $aiSummary = $summaryResult['data']['summary'] ?? '요약 생성 실패';

            $pageSummary = PageSummary::create([
                'document_summary_id' => $data['document_summary_id'],
                'page_number' => $data['page_number'],
                'block_count' => count($data['blocks']),
                'ai_summary' => $aiSummary,
            ]);

            return [
                'success' => true,
                'data' => $pageSummary,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save page summary: ' . $e->getMessage(),
            ];
        }
    }
}
