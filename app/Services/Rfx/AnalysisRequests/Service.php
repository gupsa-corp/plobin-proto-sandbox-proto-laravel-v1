<?php

namespace App\Services\Rfx\AnalysisRequests;

class Service
{
    public function execute(array $filters = []): array
    {
        $requests = [
            [
                'id' => 1,
                'title' => '프로젝트 계획서 분석 요청',
                'description' => '2024년 디지털 전환 프로젝트 계획서의 리스크 분석과 일정 검토가 필요합니다.',
                'status' => 'in_progress',
                'priority' => 'high',
                'requester' => '김매니저',
                'assignee' => '이분석',
                'createdAt' => '2024-10-09 09:00:00',
                'requiredBy' => '2024-10-15',
                'documentCount' => 3,
                'estimatedHours' => 8,
                'completedPercentage' => 65
            ],
            [
                'id' => 2,
                'title' => 'API 문서 품질 검토',
                'description' => '새로운 API 가이드 문서의 완성도와 누락된 내용을 확인해주세요.',
                'status' => 'pending',
                'priority' => 'medium',
                'requester' => '박개발',
                'assignee' => null,
                'createdAt' => '2024-10-09 11:30:00',
                'requiredBy' => '2024-10-20',
                'documentCount' => 1,
                'estimatedHours' => 4,
                'completedPercentage' => 0
            ],
            [
                'id' => 3,
                'title' => '회의록 주요 내용 추출',
                'description' => '월간 회의록에서 액션 아이템과 결정사항을 자동으로 추출해주세요.',
                'status' => 'completed',
                'priority' => 'low',
                'requester' => '최비서',
                'assignee' => '이분석',
                'createdAt' => '2024-10-08 14:00:00',
                'requiredBy' => '2024-10-10',
                'documentCount' => 5,
                'estimatedHours' => 2,
                'completedPercentage' => 100,
                'completedAt' => '2024-10-09 16:30:00'
            ],
            [
                'id' => 4,
                'title' => '사용자 매뉴얼 개선점 분석',
                'description' => '현재 사용자 매뉴얼의 가독성과 누락된 내용을 분석하여 개선점을 제시해주세요.',
                'status' => 'cancelled',
                'priority' => 'medium',
                'requester' => '홍기획',
                'assignee' => null,
                'createdAt' => '2024-10-07 10:15:00',
                'requiredBy' => '2024-10-14',
                'documentCount' => 2,
                'estimatedHours' => 6,
                'completedPercentage' => 0,
                'cancelledAt' => '2024-10-08 12:00:00',
                'cancelReason' => '요구사항 변경으로 인한 취소'
            ],
            [
                'id' => 5,
                'title' => '데이터 분석 리포트 검증',
                'description' => '분기별 데이터 분석 리포트의 정확성과 일관성을 검증해주세요.',
                'status' => 'pending',
                'priority' => 'high',
                'requester' => '김데이터',
                'assignee' => null,
                'createdAt' => '2024-10-09 16:45:00',
                'requiredBy' => '2024-10-12',
                'documentCount' => 1,
                'estimatedHours' => 12,
                'completedPercentage' => 0
            ]
        ];

        if (!empty($filters['status'])) {
            $requests = array_filter($requests, function($request) use ($filters) {
                return $request['status'] === $filters['status'];
            });
        }

        if (!empty($filters['priority'])) {
            $requests = array_filter($requests, function($request) use ($filters) {
                return $request['priority'] === $filters['priority'];
            });
        }

        if (!empty($filters['date'])) {
            $requests = array_filter($requests, function($request) use ($filters) {
                return date('Y-m-d', strtotime($request['createdAt'])) === $filters['date'];
            });
        }

        return array_values($requests);
    }

    public function createRequest(array $data): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => rand(100, 999),
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'requiredBy' => $data['requiredBy'],
                'status' => 'pending',
                'createdAt' => now()->format('Y-m-d H:i:s')
            ]
        ];
    }

    public function updateStatus($requestId, $status): array
    {
        return [
            'success' => true,
            'message' => '요청 상태가 업데이트되었습니다.'
        ];
    }

    public function updatePriority($requestId, $priority): array
    {
        return [
            'success' => true,
            'message' => '요청 우선순위가 업데이트되었습니다.'
        ];
    }

    public function assignRequest($requestId, $assignee): array
    {
        return [
            'success' => true,
            'message' => '요청이 담당자에게 배정되었습니다.'
        ];
    }

    public function deleteRequest($requestId): array
    {
        return [
            'success' => true,
            'message' => '요청이 삭제되었습니다.'
        ];
    }

    public function getAvailableAssignees(): array
    {
        return [
            ['id' => 1, 'name' => '이분석', 'role' => '선임 분석가'],
            ['id' => 2, 'name' => '박검토', 'role' => '검토 전문가'],
            ['id' => 3, 'name' => '최AI', 'role' => 'AI 엔지니어'],
            ['id' => 4, 'name' => '정품질', 'role' => '품질 관리자']
        ];
    }
}