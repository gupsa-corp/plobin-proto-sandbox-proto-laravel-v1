<?php

namespace App\Services\Rfx\AnalysisSnapshot\GetSnapshotDetail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute($snapshotId): array
    {
        try {
            $snapshot = DB::table('rfx_analysis_snapshots')
                ->where('id', $snapshotId)
                ->first();

            if (!$snapshot) {
                return [
                    'summary' => '스냅샷을 찾을 수 없습니다.',
                    'keywords' => [],
                    'categories' => [],
                    'extractedData' => []
                ];
            }

            return [
                'summary' => $snapshot->summary ?? '',
                'keywords' => json_decode($snapshot->keywords, true) ?? [],
                'categories' => json_decode($snapshot->categories, true) ?? [],
                'extractedData' => json_decode($snapshot->extracted_data, true) ?? []
            ];

        } catch (\Exception $e) {
            Log::error('스냅샷 상세 조회 실패', [
                'snapshot_id' => $snapshotId,
                'error' => $e->getMessage()
            ]);

            return [
                'summary' => '스냅샷 조회 중 오류가 발생했습니다.',
                'keywords' => [],
                'categories' => [],
                'extractedData' => []
            ];
        }
    }
}
