<?php

namespace App\Services\Rfx\AnalysisSnapshot\SaveSnapshot;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Service
{
    public function execute($ocrRequestId, $analysisResult, $reason = null): array
    {
        try {
            // 1. rfx_ai_analysis_requests에서 file_id 조회
            $request = DB::table('rfx_ai_analysis_requests')
                ->where('id', $ocrRequestId)
                ->first();

            $fileId = $request ? $request->id : null;

            // 2. 기존 스냅샷들의 is_latest를 false로 변경
            DB::table('rfx_analysis_snapshots')
                ->where('ocr_request_id', $ocrRequestId)
                ->update(['is_latest' => false]);

            // 3. 스냅샷 데이터 준비 (OCR 원본 응답 포함)
            $snapshotData = [
                'summary' => $analysisResult['summary'] ?? '',
                'keywords' => $analysisResult['keywords'] ?? [],
                'categories' => $analysisResult['categories'] ?? [],
                'extractedData' => $analysisResult['extractedData'] ?? [],
                'ocrRawData' => $analysisResult['ocrRawData'] ?? null  // OCR API 원본 응답
            ];

            // 4. 새 스냅샷 저장
            $snapshotId = DB::table('rfx_analysis_snapshots')->insertGetId([
                'ocr_request_id' => $ocrRequestId,
                'file_id' => $fileId,
                'version_timestamp' => date('YmdHis'),
                'version_type' => 'reanalysis',
                'snapshot_data' => json_encode($snapshotData),
                'summary' => $analysisResult['summary'] ?? null,
                'keywords' => json_encode($analysisResult['keywords'] ?? []),
                'categories' => json_encode($analysisResult['categories'] ?? []),
                'extracted_data' => json_encode($analysisResult['extractedData'] ?? []),
                'is_latest' => true,
                'created_by_user_id' => Auth::id(),
                'snapshot_reason' => $reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('스냅샷 저장 성공', [
                'snapshot_id' => $snapshotId,
                'ocr_request_id' => $ocrRequestId
            ]);

            return [
                'success' => true,
                'snapshot_id' => $snapshotId,
                'message' => '스냅샷이 성공적으로 저장되었습니다.'
            ];

        } catch (\Exception $e) {
            Log::error('스냅샷 저장 실패', [
                'ocr_request_id' => $ocrRequestId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => '스냅샷 저장 중 오류가 발생했습니다: ' . $e->getMessage()
            ];
        }
    }
}
