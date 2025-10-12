<?php

namespace App\Services\Pms\Dashboard;

use App\Models\Pms\Project;

/**
 * PMS 도메인 대시보드 서비스
 */
class Service
{
    public function execute(): array
    {
        // 통계 데이터 계산
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'in_progress')->count();

        // 완료된 작업 수 (진행률 100인 프로젝트)
        $completedTasks = Project::where('progress', 100)->count();

        // 대기중인 작업 수 (planning 상태)
        $pendingTasks = Project::where('status', 'planning')->count();

        // 최근 프로젝트 (최신 5개)
        $recentProjects = Project::orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->title,
                    'status' => $project->status,
                    'progress' => $project->progress,
                    'dueDate' => $project->due_date?->format('Y-m-d') ?? $project->end_date?->format('Y-m-d') ?? 'N/A'
                ];
            })->toArray();

        // 내 작업 (우선순위 높은 순으로 5개)
        $tasks = Project::whereIn('status', ['planning', 'in_progress'])
            ->orderByRaw("CASE
                WHEN priority = 'high' THEN 1
                WHEN priority = 'medium' THEN 2
                WHEN priority = 'low' THEN 3
                ELSE 4
            END")
            ->limit(5)
            ->get()
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'priority' => $project->priority,
                    'dueDate' => $project->due_date?->format('Y-m-d') ?? $project->end_date?->format('Y-m-d') ?? 'N/A'
                ];
            })->toArray();

        // 알림 (임시 데이터 - 추후 알림 시스템 구현 시 교체)
        $notifications = [
            [
                'id' => 1,
                'message' => '새로운 프로젝트가 할당되었습니다.',
                'time' => '10분 전'
            ],
            [
                'id' => 2,
                'message' => '작업 마감일이 다가왔습니다.',
                'time' => '1시간 전'
            ]
        ];

        return [
            'stats' => [
                'totalProjects' => $totalProjects,
                'activeProjects' => $activeProjects,
                'completedTasks' => $completedTasks,
                'pendingTasks' => $pendingTasks
            ],
            'recentProjects' => $recentProjects,
            'tasks' => $tasks,
            'notifications' => $notifications
        ];
    }
}