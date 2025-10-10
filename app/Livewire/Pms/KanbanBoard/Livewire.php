<?php

namespace App\Livewire\Pms\KanbanBoard;

use Livewire\Component;
use App\Services\Pms\Kanban\Service;
use App\Models\Pms\Project;

class Livewire extends Component
{
    public $columns = [];
    public $projects = [];

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

    public function moveTask($taskId, $fromColumn, $toColumn)
    {
        // DB에서 프로젝트 찾기
        $project = Project::find($taskId);

        if (!$project) {
            return;
        }

        // 상태 업데이트
        $project->status = $toColumn;

        // 상태에 따라 진행률 자동 조정
        if ($toColumn === 'completed') {
            $project->progress = 100;
        } elseif ($toColumn === 'review' && $project->progress < 80) {
            $project->progress = 80;
        } elseif ($toColumn === 'in_progress' && $project->progress == 0) {
            $project->progress = 25;
        }

        // DB에 저장
        $project->save();

        // 화면 데이터 다시 로드
        $this->loadKanbanData();
    }

    public function render()
    {
        return view('700-page-pms-kanban-board.000-index')
            ->layout('300-layout-common.000-app');
    }
}
