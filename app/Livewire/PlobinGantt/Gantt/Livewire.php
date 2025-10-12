<?php

namespace App\Livewire\PlobinGantt\Gantt;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\PlobinGantt\GetGanttData\Service as GetGanttDataService;

class Livewire extends Component
{
    public $projectId;
    public $projects;
    public $tasks;

    public function mount()
    {
        $this->projects = DB::table('plobin_gantt_projects')
            ->whereNull('deleted_at')
            ->where('active', true)
            ->select('id', 'name', 'identifier')
            ->get();

        if ($this->projects->isNotEmpty()) {
            $this->projectId = $this->projects->first()->id;
            $this->loadTasks();
        }
    }

    public function loadTasks()
    {
        if (!$this->projectId) {
            $this->tasks = [];
            return;
        }

        $service = new GetGanttDataService();
        $this->tasks = $service->execute($this->projectId);
    }

    public function updatedProjectId()
    {
        $this->loadTasks();
        $this->dispatch('tasksUpdated', $this->tasks);
    }

    public function render()
    {
        return view('livewire.plobin-gantt.gantt.livewire');
    }
}
