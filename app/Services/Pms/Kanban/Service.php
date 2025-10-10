<?php

namespace App\Services\Pms\Kanban;

use App\Models\Pms\Project;

/**
 * PMS 도메인 칸반 보드 서비스
 */
class Service
{
    public function execute(): array
    {
        $projects = Project::all()->map(function($project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status,
                'assignee' => $project->assignee,
                'priority' => $project->priority,
                'dueDate' => $project->due_date?->format('Y-m-d'),
                'tags' => $project->tags ?? [],
                'progress' => $project->progress
            ];
        })->toArray();

        return [
            'columns' => [
                [
                    'id' => 'planning',
                    'title' => '계획중',
                    'color' => 'bg-yellow-50',
                    'borderColor' => 'border-yellow-200',
                    'order' => 1
                ],
                [
                    'id' => 'in_progress',
                    'title' => '진행중',
                    'color' => 'bg-blue-50',
                    'borderColor' => 'border-blue-200',
                    'order' => 2
                ],
                [
                    'id' => 'review',
                    'title' => '검토중',
                    'color' => 'bg-purple-50',
                    'borderColor' => 'border-purple-200',
                    'order' => 3
                ],
                [
                    'id' => 'completed',
                    'title' => '완료',
                    'color' => 'bg-green-50',
                    'borderColor' => 'border-green-200',
                    'order' => 4
                ]
            ],
            'projects' => $projects
        ];
    }
}