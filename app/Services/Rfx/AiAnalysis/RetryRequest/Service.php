<?php

namespace App\Services\Rfx\AiAnalysis\RetryRequest;

use Illuminate\Support\Facades\DB;
use App\Jobs\Rfx\ProcessOcrAnalysis\Jobs as ProcessOcrAnalysisJob;

class Service
{
    public function execute(int $requestId): array
    {
        try {
            // 1. 요청 ID로 기존 요청 조회
            $request = DB::table('rfx_ai_analysis_requests')
                ->where('id', $requestId)
                ->first();

            if (!$request) {
                return [
                    'success' => false,
                    'message' => '요청을 찾을 수 없습니다.',
                ];
            }

            // 2. 상태를 'pending'으로 변경 및 오류 메시지 초기화
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $requestId)
                ->update([
                    'status' => 'pending',
                    'progress' => 0,
                    'error_message' => null,
                    'started_at' => null,
                    'completed_at' => null,
                    'updated_at' => now(),
                ]);

            // 3. 큐에 다시 등록
            // TODO: 실제 파일 경로를 조회하도록 수정 필요
            $filePath = 'uploads/sample-document.pdf'; // 임시
            ProcessOcrAnalysisJob::dispatch($requestId, $request->file_id, $filePath);

            return [
                'success' => true,
                'message' => '분석 요청이 재시도되었습니다.',
                'data' => [
                    'request_id' => $requestId,
                    'status' => 'pending',
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '재시도에 실패했습니다: ' . $e->getMessage(),
            ];
        }
    }
}
