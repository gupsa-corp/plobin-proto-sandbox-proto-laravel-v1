<?php

namespace App\Services\Rfx\AiAnalysis\GetRequests;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(array $filters): array
    {
        try {
            // 1. 쿼리 빌더 시작
            $query = DB::table('rfx_ai_analysis_requests');

            // 2. 필터링 조건 적용
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['analysisType'])) {
                $query->where('analysis_type', $filters['analysisType']);
            }

            if (!empty($filters['dateRange'])) {
                $this->applyDateRangeFilter($query, $filters['dateRange']);
            }

            // 3. 분석 요청 목록 조회 (최신순)
            $requests = $query->orderBy('requested_at', 'desc')->get()->map(function ($request) {
                return [
                    'id' => $request->id,
                    'fileName' => $request->file_name,
                    'fileType' => strtoupper($request->file_type),
                    'analysisType' => $request->analysis_type,
                    'status' => $request->status,
                    'progress' => $request->progress,
                    'requestedAt' => $request->requested_at,
                    'completedAt' => $request->completed_at,
                ];
            })->toArray();

            // 4. 통계 정보 계산
            $stats = [
                'total' => DB::table('rfx_ai_analysis_requests')->count(),
                'pending' => DB::table('rfx_ai_analysis_requests')->where('status', 'pending')->count(),
                'processing' => DB::table('rfx_ai_analysis_requests')->where('status', 'processing')->count(),
                'completed' => DB::table('rfx_ai_analysis_requests')->where('status', 'completed')->count(),
                'failed' => DB::table('rfx_ai_analysis_requests')->where('status', 'failed')->count(),
            ];

            return [
                'success' => true,
                'requests' => $requests,
                'stats' => $stats,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '요청 목록 조회에 실패했습니다: ' . $e->getMessage(),
                'requests' => [],
                'stats' => [],
            ];
        }
    }

    private function applyDateRangeFilter($query, string $dateRange): void
    {
        switch ($dateRange) {
            case 'today':
                $query->whereDate('requested_at', today());
                break;
            case 'week':
                $query->where('requested_at', '>=', now()->subDays(7));
                break;
            case 'month':
                $query->where('requested_at', '>=', now()->subDays(30));
                break;
        }
    }
}
