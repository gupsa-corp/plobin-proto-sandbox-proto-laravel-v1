<?php

namespace App\Services\Pms\UpdateProject;

use App\Models\Pms\Project;

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
            $priority = $params['priority'];
            $status = $params['status'];
            $completedPercentage = $params['completed_percentage'] ?? 0;

            // 프로젝트 조회
            $project = Project::find($id);

            if (!$project) {
                return [
                    'success' => false,
                    'message' => '수정할 프로젝트를 찾을 수 없습니다.',
                    'data' => null
                ];
            }

            // 프로젝트 업데이트
            $project->update([
                'title' => $title,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'priority' => $priority,
                'status' => $status,
                'progress' => $completedPercentage,
            ]);

            return [
                'success' => true,
                'message' => '프로젝트가 성공적으로 수정되었습니다.',
                'data' => [
                    'id' => $id,
                    'project' => $project
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
