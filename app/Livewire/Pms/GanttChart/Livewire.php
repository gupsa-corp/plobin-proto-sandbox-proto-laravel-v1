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