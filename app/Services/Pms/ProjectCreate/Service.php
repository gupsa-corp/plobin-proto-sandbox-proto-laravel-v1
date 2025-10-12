<?php

namespace App\Services\Pms\ProjectCreate;

use App\Models\Pms\Project;

/**
 * PMS 프로젝트 생성 서비스
 */
class Service
{
    public function execute(array $data): array
    {
        try {
            $project = Project::create([
                'title' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 'planning',
                'priority' => $data['priority'] ?? 'medium',
                'progress' => $data['progress'] ?? 0,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'assignee' => $data['assignee'] ?? null
            ]);

            return [
                'success' => true,
                'message' => "\"{$project->title}\" 프로젝트가 성공적으로 생성되었습니다.",
                'data' => [
                    'project_id' => $project->id
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '프로젝트 생성 중 오류가 발생했습니다: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}