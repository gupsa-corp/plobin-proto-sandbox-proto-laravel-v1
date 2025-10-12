<?php

namespace App\Livewire\Pms\GanttChart;

use Livewire\Component;
use App\Services\Pms\Projects\Service;

class Livewire extends Component
{
    /**
     * Livewire에서는 단순히 데이터 제공만 하고,
     * 모든 UI 로직은 Alpine.js에서 처리
     */

    public function getProjects()
    {
        $service = new Service();
        $result = $service->execute();
        return $result['data'] ?? [];
    }

    public function createProject($projectData)
    {
        $service = new \App\Services\Pms\ProjectCreate\Service();

        $params = [
            'title' => $projectData['name'] ?? '',
            'description' => $projectData['description'] ?? '',
            'start_date' => $projectData['startDate'] ?? null,
            'end_date' => $projectData['endDate'] ?? null,
            'priority' => $projectData['priority'] ?? 'medium',
            'status' => $projectData['status'] ?? 'planning',
            'completed_percentage' => $projectData['progress'] ?? 0,
        ];

        return $service->execute($params);
    }

    public function updateProject($projectId, $projectData)
    {
        $service = new \App\Services\Pms\UpdateProject\Service();

        $params = [
            'id' => $projectId,
            'title' => $projectData['name'] ?? '',
            'description' => $projectData['description'] ?? '',
            'start_date' => $projectData['startDate'] ?? null,
            'end_date' => $projectData['endDate'] ?? null,
            'priority' => $projectData['priority'] ?? 'medium',
            'status' => $projectData['status'] ?? 'planning',
            'completed_percentage' => $projectData['progress'] ?? 0,
        ];

        return $service->execute($params);
    }

    public function render()
    {
        return view('700-page-pms-gantt-chart.000-index')
            ->layout('300-layout-common.000-app');
    }
}
