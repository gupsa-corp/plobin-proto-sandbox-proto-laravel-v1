<?php

namespace App\Services\Rfx\BlockSummary\SaveSummary;

use App\Models\Rfx\DocumentSummary;

class Service
{
    /**
     * 전체 요약 데이터 저장
     *
     * @param array $data [document_id, total_pages, total_blocks]
     * @return array [success, data/message]
     */
    public function execute(array $data): array
    {
        try {
            $timestamp = now()->format('YmdHisu'); // YmdHisu 형식 (20250115143025123456)

            $summary = DocumentSummary::create([
                'document_id' => $data['document_id'],
                'total_pages' => $data['total_pages'],
                'total_blocks' => $data['total_blocks'],
                'json_version' => $timestamp,
                'document_version' => 'v1.0',
            ]);

            return [
                'success' => true,
                'data' => $summary,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save summary: ' . $e->getMessage(),
            ];
        }
    }
}
