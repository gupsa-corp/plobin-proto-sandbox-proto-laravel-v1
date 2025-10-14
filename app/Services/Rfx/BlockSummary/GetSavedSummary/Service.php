<?php

namespace App\Services\Rfx\BlockSummary\GetSavedSummary;

use App\Models\Rfx\DocumentSummary;

class Service
{
    /**
     * 저장된 요약 데이터 불러오기
     *
     * @param array $data [document_id]
     * @return array [success, data/message]
     */
    public function execute(array $data): array
    {
        try {
            $summary = DocumentSummary::where('document_id', $data['document_id'])
                ->with([
                    'pageSummaries.sectionAnalyses.versions' => function ($query) {
                        $query->orderBy('version_number', 'DESC');
                    }
                ])
                ->latest()
                ->first();

            if (!$summary) {
                return [
                    'success' => false,
                    'message' => 'No saved summary found',
                ];
            }

            return [
                'success' => true,
                'data' => $summary,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load saved summary: ' . $e->getMessage(),
            ];
        }
    }
}
