<?php

namespace App\Services\Pms\Projects;

use App\Models\Plobin\Project;

/**
 * PMS 도메인 프로젝트 관리 서비스
 */
class Service
{
    public function execute(array $filters = []): array
    {
        $query = Project::query();

        // 검색 필터 적용
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 상태 필터 적용
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 우선순위 필터 적용
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // 정렬 적용
        if (!empty($filters['sortBy'])) {
            $sortBy = $filters['sortBy'];
            $sortDirection = $filters['sortDirection'] ?? 'asc';
            
            // created_at을 created_at으로 매핑 (DB 컬럼명)
            $sortColumn = $sortBy === 'created_at' ? 'created_at' : $sortBy;
            
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $projects = $query->get();

        // 프론트엔드에서 기대하는 형식으로 변환
        $formattedProjects = $projects->map(function($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'priority' => $project->priority,
                'progress' => $project->progress,
                'startDate' => $project->start_date?->format('Y-m-d'),
                'endDate' => $project->end_date?->format('Y-m-d'),
                'team' => $project->team ?? [],
                'createdAt' => $project->created_at->format('Y-m-d H:i:s')
            ];
        })->toArray();

        return [
            'success' => true,
            'data' => $formattedProjects
        ];
    }
}