<?php

namespace App\Services\Rfx\FormExecution\ListExecutions;

class Service
{
    public function execute(array $filters = []): array
    {
        $executions = [
            [
                'id' => 1,
                'formName' => '계약서 데이터 추출',
                'status' => 'completed',
                'startedAt' => '2024-10-09 10:30:00',
                'completedAt' => '2024-10-09 10:45:00',
                'executedBy' => '김분석',
                'documentsProcessed' => 15,
                'successCount' => 14,
                'errorCount' => 1,
                'duration' => '15분',
                'resultSize' => '2.3MB'
            ],
            [
                'id' => 2,
                'formName' => '송장 데이터 검증',
                'status' => 'running',
                'startedAt' => '2024-10-09 14:20:00',
                'completedAt' => null,
                'executedBy' => '이검토',
                'documentsProcessed' => 8,
                'successCount' => 7,
                'errorCount' => 0,
                'duration' => null,
                'resultSize' => null,
                'progress' => 53
            ],
            [
                'id' => 3,
                'formName' => '회의록 요약 생성',
                'status' => 'pending',
                'startedAt' => null,
                'completedAt' => null,
                'executedBy' => '박AI',
                'documentsProcessed' => 0,
                'successCount' => 0,
                'errorCount' => 0,
                'duration' => null,
                'resultSize' => null,
                'scheduledAt' => '2024-10-09 16:00:00'
            ],
            [
                'id' => 4,
                'formName' => '재무제표 분석',
                'status' => 'failed',
                'startedAt' => '2024-10-09 09:15:00',
                'completedAt' => '2024-10-09 09:22:00',
                'executedBy' => '최재무',
                'documentsProcessed' => 3,
                'successCount' => 0,
                'errorCount' => 3,
                'duration' => '7분',
                'resultSize' => null,
                'errorMessage' => '문서 형식이 지원되지 않습니다.'
            ],
            [
                'id' => 5,
                'formName' => '품질 보고서 검토',
                'status' => 'cancelled',
                'startedAt' => '2024-10-08 15:30:00',
                'completedAt' => '2024-10-08 15:35:00',
                'executedBy' => '정품질',
                'documentsProcessed' => 2,
                'successCount' => 1,
                'errorCount' => 0,
                'duration' => '5분',
                'resultSize' => null,
                'cancelReason' => '사용자 요청으로 취소'
            ]
        ];

        if (!empty($filters['status'])) {
            $executions = array_filter($executions, function($execution) use ($filters) {
                return $execution['status'] === $filters['status'];
            });
        }

        return array_values($executions);
    }
}