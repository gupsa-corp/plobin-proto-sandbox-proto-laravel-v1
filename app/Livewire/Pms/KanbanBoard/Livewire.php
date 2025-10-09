<?php

namespace App\Livewire\Pms\KanbanBoard;

use Livewire\Component;
use App\Services\Pms\Kanban\Service;

class Livewire extends Component
{
    public $columns;
    public $projects;
    public $showTaskForm = false;
    public $editingTask = null;
    public $selectedColumn = null;

    public function mount()
    {
        $this->loadKanbanData();
    }

    public function loadKanbanData()
    {
        $service = new Service();
        $data = $service->execute();
        
        $this->columns = $data['columns'];
        $this->projects = $data['projects'];
    }

    public function openTaskForm($columnId = null, $taskId = null)
    {
        $this->selectedColumn = $columnId;
        $this->editingTask = $taskId;
        $this->showTaskForm = true;
    }

    public function closeTaskForm()
    {
        $this->showTaskForm = false;
        $this->editingTask = null;
        $this->selectedColumn = null;
    }

    public function moveTask($taskId, $fromColumn, $toColumn)
    {
        // 칸반 보드에서 카드 이동 로직
        foreach ($this->projects as &$project) {
            if ($project['id'] == $taskId) {
                $project['status'] = $toColumn;
                break;
            }
        }
        $this->loadKanbanData();
    }

    public function render()
    {
        return view('700-page-pms-kanban-board/000-index');
    }
}