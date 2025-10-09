<?php

namespace App\Livewire\Pms\ProjectList;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Pms\Projects\Service;
use App\Services\Pms\ProjectCreate\Service as ProjectCreateService;
use App\Services\Pms\ProjectUpdate\Service as ProjectUpdateService;

class Livewire extends Component
{
    use WithPagination;

    public $projects;
    public $search = '';
    public $status = '';
    public $priority = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $viewMode = 'grid'; // grid 또는 table
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingProjectId = null;
    public $projectForm = [
        'name' => '',
        'description' => '',
        'status' => 'planning',
        'priority' => 'medium',
        'start_date' => '',
        'end_date' => '',
        'progress' => 0
    ];
    public $successMessage = '';

    public function mount()
    {
        $this->loadProjects();
    }

    public function updatedSearch()
    {
        $this->loadProjects();
    }

    public function updatedStatus()
    {
        $this->loadProjects();
    }

    public function updatedPriority()
    {
        $this->loadProjects();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadProjects();
    }

    public function openCreateModal()
    {
        \Log::info('openCreateModal called');
        $this->resetProjectForm();
        $this->showCreateModal = true;
        $this->successMessage = 'Create modal opened!';
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetProjectForm();
    }

    public function resetProjectForm()
    {
        $this->projectForm = [
            'name' => '',
            'description' => '',
            'status' => 'planning',
            'priority' => 'medium',
            'start_date' => '',
            'end_date' => '',
            'progress' => 0
        ];
    }

    public function createProject()
    {
        $this->validate([
            'projectForm.name' => 'required|min:3',
            'projectForm.description' => 'required|min:10',
            'projectForm.start_date' => 'required|date',
            'projectForm.end_date' => 'required|date|after:projectForm.start_date'
        ], [
            'projectForm.name.required' => '프로젝트명을 입력해주세요.',
            'projectForm.name.min' => '프로젝트명은 3자 이상이어야 합니다.',
            'projectForm.description.required' => '설명을 입력해주세요.',
            'projectForm.description.min' => '설명은 10자 이상이어야 합니다.',
            'projectForm.start_date.required' => '시작일을 선택해주세요.',
            'projectForm.end_date.required' => '마감일을 선택해주세요.',
            'projectForm.end_date.after' => '마감일은 시작일보다 늦어야 합니다.'
        ]);

        $service = new ProjectCreateService();
        $result = $service->execute($this->projectForm);
        
        if ($result['success']) {
            $this->successMessage = $result['message'];
            session()->flash('message', $this->successMessage);
            $this->closeCreateModal();
            $this->loadProjects();
        } else {
            $this->addError('projectForm', $result['message']);
        }
    }

    public function viewProject($projectId)
    {
        // 프로젝트 상세 정보 조회
        $project = collect($this->projects)->firstWhere('id', $projectId);
        if ($project) {
            $this->successMessage = "프로젝트 '{$project['name']}' 상세 정보: 진행률 {$project['progress']}%, 상태: {$this->getStatusText($project['status'])}";
            session()->flash('message', $this->successMessage);
        }
    }

    private function getStatusText($status)
    {
        return match($status) {
            'planning' => '계획중',
            'in_progress' => '진행중',
            'completed' => '완료',
            'pending' => '대기중',
            default => '알 수 없음'
        };
    }

    public function editProject($projectId)
    {
        $project = collect($this->projects)->firstWhere('id', $projectId);
        if ($project) {
            $this->editingProjectId = $projectId;
            $this->projectForm = [
                'name' => $project['name'],
                'description' => $project['description'],
                'status' => $project['status'],
                'priority' => $project['priority'],
                'start_date' => $project['startDate'],
                'end_date' => $project['endDate'],
                'progress' => $project['progress']
            ];
            $this->showEditModal = true;
        }
    }

    public function updateProject()
    {
        $this->validate([
            'projectForm.name' => 'required|min:3',
            'projectForm.description' => 'required|min:10',
            'projectForm.start_date' => 'required|date',
            'projectForm.end_date' => 'required|date|after:projectForm.start_date'
        ]);

        $service = new ProjectUpdateService();
        $result = $service->execute($this->editingProjectId, $this->projectForm);
        
        if ($result['success']) {
            $this->successMessage = $result['message'];
            session()->flash('message', $this->successMessage);
            $this->closeEditModal();
            $this->loadProjects();
        } else {
            $this->addError('projectForm', $result['message']);
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingProjectId = null;
        $this->resetProjectForm();
    }

    public function switchToTableView()
    {
        return redirect()->route('pms.table-view');
    }

    public function loadProjects()
    {
        $service = new Service();
        $result = $service->execute([
            'search' => $this->search,
            'status' => $this->status,
            'priority' => $this->priority,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection
        ]);
        $this->projects = $result['data'];
    }

    public function render()
    {
        return view('700-page-pms-project-list/000-index')
            ->layout('layouts.app');
    }
}