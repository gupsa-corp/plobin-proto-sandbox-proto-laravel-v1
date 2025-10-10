<?php

namespace App\Services\Pms\CreateAnalysisRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Service
{
    public function execute(array $params): array
    {
        try {
            $requestId = DB::table('plobin_analysis_requests')->insertGetId([
                'title' => $params['title'],
                'description' => $params['description'] ?? null,
                'start_date' => $params['start_date'] ?? null,
                'end_date' => $params['end_date'] ?? null,
                'estimated_hours' => $params['estimated_hours'] ?? null,
                'priority' => $params['priority'],
                'requester_id' => $params['requester_id'],
                'status' => 'pending',
                'completed_percentage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => '분석 요청이 성공적으로 생성되었습니다.',
                'data' => [
                    'request_id' => $requestId,
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('분석 요청 생성 실패: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => '분석 요청 생성에 실패했습니다.',
                'data' => null,
            ];
        }
    }
}
