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
        // TODO: 프로젝트 생성 서비스 호출
        // Service를 통해 생성 로직 구현 필요

        return [
            'success' => true,
            'message' => '프로젝트가 생성되었습니다.'
        ];
    }

    public function updateProject($projectId, $projectData)
    {
        // TODO: 프로젝트 업데이트 서비스 호출
        // Service를 통해 업데이트 로직 구현 필요

        return [
            'success' => true,
            'message' => '프로젝트가 업데이트되었습니다.'
        ];
    }

    public function render()
    {
        return view('700-page-pms-gantt-chart.000-index')
            ->layout('300-layout-common.000-app');
    }
}
