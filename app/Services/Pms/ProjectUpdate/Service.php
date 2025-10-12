<?php

namespace App\Services\Pms\ProjectUpdate;

use App\Models\Pms\Project;

/**
 * PMS 프로젝트 수정 서비스
 */
class Service
{
    public function execute(int $projectId, array $data): array
    {
        try {
            $project = Project::findOrFail($projectId);
            
            $project->update([
                'title' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'],
                'priority' => $data['priority'],
                'progress' => $data['progress'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'assignee' => $data['assignee'] ?? null
            ]);

            return [
                'success' => true,
                'message' => "프로젝트 '{$project->title}'이 성공적으로 수정되었습니다.",
                'data' => [
                    'project_id' => $project->id
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '프로젝트 수정 중 오류가 발생했습니다: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}