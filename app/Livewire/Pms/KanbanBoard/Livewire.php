<?php

namespace App\Livewire\Pms\KanbanBoard;

use Livewire\Component;
use App\Services\Pms\Kanban\Service;
use App\Models\Pms\Project;

class Livewire extends Component
{
    public $columns = [];
    public $projects = [];
    public $selectedProjectId = null;

    // 편집 필드
    public $editTitle = '';
    public $editDescription = '';
    public $editProgress = 0;
    public $editStatus = 'planning';
    public $editPriority = 'medium';
    public $editAssignee = '';
    public $editDueDate = '';

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

    public function selectProject($projectId)
    {
        $this->selectedProjectId = $projectId;

        // DB에서 프로젝트 찾기
        $project = Project::find($projectId);

        if ($project) {
            // 편집 필드 초기화
            $this->editTitle = $project->title;
            $this->editDescription = $project->description;
            $this->editProgress = $project->progress;
            $this->editStatus = $project->status;
            $this->editPriority = $project->priority;
            $this->editAssignee = $project->assignee;
            $this->editDueDate = $project->due_date?->format('Y-m-d') ?? '';

            // JavaScript로 모달 열기 직접 처리 (Alpine 변수 직접 변경)
            $this->js('showProjectModal = true');
        }
    }

    public function saveProject()
    {
        // DB에서 프로젝트 찾기
        $project = Project::find($this->selectedProjectId);

        if (!$project) {
            return;
        }

        // 프로젝트 업데이트
        $project->title = $this->editTitle;
        $project->description = $this->editDescription;
        $project->progress = $this->editProgress;
        $project->status = $this->editStatus;
        $project->priority = $this->editPriority;
        $project->assignee = $this->editAssignee;
        $project->due_date = $this->editDueDate;

        // DB에 저장
        $project->save();

        // 화면 데이터 다시 로드
        $this->loadKanbanData();

        // 모달 닫기
        $this->js('showProjectModal = false');
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
