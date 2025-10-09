<?php

namespace App\Livewire\Pms\TableView;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Pms\Projects\Service;

class Livewire extends Component
{
    use WithPagination;

    public $projects;
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $selectedColumns = ['name', 'status', 'priority', 'progress', 'team', 'dates'];
    public $showColumnSelector = false;
    public $showProjectForm = false;
    public $editingProject = null;

    public function mount()
    {
        $this->loadProjects();
    }

    public function updatedSearch()
    {
        $this->loadProjects();
    }

    public function updatedStatusFilter()
    {
        $this->loadProjects();
    }

    public function updatedPriorityFilter()
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

    public function toggleColumn($column)
    {
        if (in_array($column, $this->selectedColumns)) {
            $this->selectedColumns = array_diff($this->selectedColumns, [$column]);
        } else {
            $this->selectedColumns[] = $column;
        }
    }

    public function openProjectForm($projectId = null)
    {
        $this->editingProject = $projectId;
        $this->showProjectForm = true;
    }

    public function closeProjectForm()
    {
        $this->showProjectForm = false;
        $this->editingProject = null;
    }

    public function loadProjects()
    {
        $service = new Service();
        $result = $service->execute([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'priority' => $this->priorityFilter,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection
        ]);
        $this->projects = $result['data'];
    }

    public function render()
    {
        return view('700-page-pms-table-view/000-index')
            ->layout('layouts.app');
    }
}