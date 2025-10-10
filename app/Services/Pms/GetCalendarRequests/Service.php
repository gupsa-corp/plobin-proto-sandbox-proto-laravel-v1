<?php

namespace App\Services\Pms\GetCalendarRequests;

use App\Models\Pms\Project;
use Carbon\Carbon;

class Service
{
    public function execute(array $params): array
    {
        $priority = $params['priority'] ?? '';
        $status = $params['status'] ?? '';
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;

        $query = Project::query();

        // 필터 적용
        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // 날짜 범위 필터 (start_date 또는 end_date가 범위 내에 있으면 포함)
        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });
        }

        $projects = $query->orderBy('start_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Service 인스턴스 생성
        $typeService = new \App\Services\Pms\GetTypeByStatus\Service();
        $colorService = new \App\Services\Pms\GetColorByPriority\Service();

        // 캘린더 데이터로 변환
        $calendarData = $projects->map(function ($project) use ($typeService, $colorService) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'start_date' => $project->start_date?->format('Y-m-d'),
                'end_date' => $project->end_date?->format('Y-m-d'),
                'date' => $project->end_date?->format('Y-m-d') ?? $project->start_date?->format('Y-m-d'), // 캘린더 표시용 날짜 (종료일 우선)
                'status' => $project->status,
                'priority' => $project->priority,
                'requester' => null, // 프로젝트는 요청자 개념이 없음
                'assignee' => $project->assignee,
                'estimated_hours' => null, // 프로젝트는 예상 시간 대신 진행률 사용
                'completed_percentage' => $project->progress,
                'type' => $typeService->execute($project->status),
                'color' => $colorService->execute($project->priority),
                'created_at' => $project->created_at->format('Y-m-d H:i:s'),
                'completed_at' => $project->status === 'completed' ? $project->updated_at->format('Y-m-d H:i:s') : null,
            ];
        })->toArray();

        return [
            'success' => true,
            'data' => $calendarData,
            'total' => count($calendarData),
        ];
    }
}
