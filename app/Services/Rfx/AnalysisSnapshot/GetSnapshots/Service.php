<?php

namespace App\Services\Rfx\AnalysisSnapshot\GetSnapshots;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute($ocrRequestId): array
    {
        try {
            $snapshots = DB::table('rfx_analysis_snapshots')
                ->where('ocr_request_id', $ocrRequestId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($snapshot) {
                    return [
                        'id' => $snapshot->id,
                        'version_timestamp' => $snapshot->version_timestamp,
                        'version_type' => $snapshot->version_type,
                        'is_latest' => (bool) $snapshot->is_latest,
                        'created_at' => $snapshot->created_at,
                        'created_by_user_id' => $snapshot->created_by_user_id,
                        'snapshot_reason' => $snapshot->snapshot_reason
                    ];
                })
                ->toArray();

            return $snapshots;

        } catch (\Exception $e) {
            Log::error('스냅샷 목록 조회 실패', [
                'ocr_request_id' => $ocrRequestId,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }
}
