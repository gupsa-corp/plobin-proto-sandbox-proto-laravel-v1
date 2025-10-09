<?php

namespace App\Services\Pms\Projects;

/**
 * PMS 도메인 프로젝트 관리 서비스
 */
class Service
{
    public function execute(array $filters = []): array
    {
        $projects = [
            [
                'id' => 1,
                'name' => '웹사이트 리뉴얼 프로젝트',
                'description' => '기존 웹사이트의 완전한 리뉴얼을 진행합니다.',
                'status' => 'in_progress',
                'priority' => 'high',
                'progress' => 75,
                'startDate' => '2024-09-01',
                'endDate' => '2024-12-15',
                'team' => ['김개발', '이디자인', '박기획'],
                'createdAt' => '2024-09-01 09:00:00'
            ],
            [
                'id' => 2,
                'name' => '모바일 앱 개발',
                'description' => '고객용 모바일 애플리케이션 개발',
                'status' => 'planning',
                'priority' => 'medium',
                'progress' => 25,
                'startDate' => '2024-10-01',
                'endDate' => '2024-12-30',
                'team' => ['최개발', '임디자인'],
                'createdAt' => '2024-10-01 10:00:00'
            ],
            [
                'id' => 3,
                'name' => 'API 서버 구축',
                'description' => '마이크로서비스 아키텍처 기반 API 서버 구축',
                'status' => 'completed',
                'priority' => 'high',
                'progress' => 100,
                'startDate' => '2024-08-01',
                'endDate' => '2024-09-30',
                'team' => ['정백엔드', '조데브옵스'],
                'createdAt' => '2024-08-01 08:00:00'
            ],
            [
                'id' => 4,
                'name' => '데이터베이스 최적화',
                'description' => '기존 데이터베이스 성능 최적화 작업',
                'status' => 'pending',
                'priority' => 'low',
                'progress' => 0,
                'startDate' => '2024-11-01',
                'endDate' => '2024-11-30',
                'team' => ['한디비에이'],
                'createdAt' => '2024-10-15 14:00:00'
            ]
        ];

        // 검색 필터 적용
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $projects = array_filter($projects, function($project) use ($search) {
                return strpos(strtolower($project['name']), $search) !== false ||
                       strpos(strtolower($project['description']), $search) !== false;
            });
        }

        // 상태 필터 적용
        if (!empty($filters['status'])) {
            $projects = array_filter($projects, function($project) use ($filters) {
                return $project['status'] === $filters['status'];
            });
        }

        // 우선순위 필터 적용
        if (!empty($filters['priority'])) {
            $projects = array_filter($projects, function($project) use ($filters) {
                return $project['priority'] === $filters['priority'];
            });
        }

        // 정렬 적용
        if (!empty($filters['sortBy'])) {
            $sortBy = $filters['sortBy'];
            $sortDirection = $filters['sortDirection'] ?? 'asc';
            
            usort($projects, function($a, $b) use ($sortBy, $sortDirection) {
                // created_at을 createdAt으로 매핑
                $sortKey = $sortBy === 'created_at' ? 'createdAt' : $sortBy;
                
                if (!isset($a[$sortKey]) || !isset($b[$sortKey])) {
                    return 0;
                }
                
                $result = strcmp($a[$sortKey], $b[$sortKey]);
                return $sortDirection === 'desc' ? -$result : $result;
            });
        }

        return [
            'success' => true,
            'data' => array_values($projects)
        ];
    }
}