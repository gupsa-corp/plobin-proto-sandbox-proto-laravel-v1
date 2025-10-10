<?php

namespace App\Livewire\Pms\GanttChart;

use Livewire\Component;
use App\Services\Pms\Projects\Service;

class Livewire extends Component
{
    public $projects;
    public $timeRange = '3months';
    public $viewMode = 'month';
    public $selectedProject = null;

    public function mount()
    {
        $this->loadGanttData();
    }

    public function changeTimeRange($range)
    {
        $this->timeRange = $range;
        $this->loadGanttData();
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->loadGanttData();
    }

    public function selectProject($projectId)
    {
        $this->selectedProject = $projectId;
    }

    public function updateProjectDates($projectId, $startDate, $endDate)
    {
        // 프로젝트 날짜 업데이트 로직
        foreach($this->projects as &$project) {
            if($project['id'] == $projectId) {
                $project['startDate'] = $startDate;
                $project['endDate'] = $endDate;
                break;
            }
        }
        
        // 실제로는 서비스를 통해 데이터베이스 업데이트 필요
        $this->dispatch('project-updated', ['projectId' => $projectId, 'message' => '프로젝트 일정이 업데이트되었습니다.']);
    }

    public function loadGanttData()
    {
        $service = new Service();
        $result = $service->execute();
        $this->projects = $result['data'];
    }

    public function render()
    {
        return view('700-page-pms-gantt-chart/000-index');
    }
}