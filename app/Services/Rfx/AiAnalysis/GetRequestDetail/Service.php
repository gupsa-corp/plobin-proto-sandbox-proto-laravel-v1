<?php

namespace App\Services\Rfx\AiAnalysis\GetRequestDetail;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(int $requestId): array
    {
        try {
            // 1. 요청 ID로 상세 정보 조회
            $request = DB::table('rfx_ai_analysis_requests')
                ->where('id', $requestId)
                ->first();

            if (!$request) {
                return [
                    'success' => false,
                    'message' => '요청을 찾을 수 없습니다.',
                ];
            }

            // 2. 분석 결과 데이터 포함
            $result = null;
            if ($request->result) {
                $result = json_decode($request->result, true);
            }

            $data = [
                'id' => $request->id,
                'fileName' => $request->file_name,
                'fileType' => strtoupper($request->file_type),
                'analysisType' => $request->analysis_type,
                'status' => $request->status,
                'progress' => $request->progress,
                'requestedAt' => $request->requested_at,
                'completedAt' => $request->completed_at,
                'result' => $result,
                'errorMessage' => $request->error_message,
            ];

            return [
                'success' => true,
                'data' => $data,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '요청 상세 정보 조회에 실패했습니다: ' . $e->getMessage(),
            ];
        }
    }
}
