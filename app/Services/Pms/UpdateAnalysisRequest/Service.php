<?php

namespace App\Services\Pms\UpdateAnalysisRequest;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(array $params): array
    {
        try {
            $id = $params['id'];
            $title = $params['title'];
            $description = $params['description'] ?? null;
            $startDate = $params['start_date'] ?? null;
            $endDate = $params['end_date'] ?? null;
            $estimatedHours = $params['estimated_hours'] ?? null;
            $priority = $params['priority'];
            $status = $params['status'];
            $completedPercentage = $params['completed_percentage'] ?? 0;

            // 분석 요청 업데이트
            $updated = DB::table('plobin_analysis_requests')
                ->where('id', $id)
                ->update([
                    'title' => $title,
                    'description' => $description,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'estimated_hours' => $estimatedHours,
                    'priority' => $priority,
                    'status' => $status,
                    'completed_percentage' => $completedPercentage,
                    'updated_at' => now(),
                ]);

            if ($updated) {
                return [
                    'success' => true,
                    'message' => '분석 요청이 성공적으로 수정되었습니다.',
                    'data' => [
                        'id' => $id
                    ]
                ];
            }

            return [
                'success' => false,
                'message' => '수정할 분석 요청을 찾을 수 없습니다.',
                'data' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '분석 요청 수정 중 오류가 발생했습니다: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
