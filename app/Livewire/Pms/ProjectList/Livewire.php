<?php

namespace App\Livewire\Pms\ProjectList;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Pms\Projects\Service;

class Livewire extends Component
{
    use WithPagination;

    public $projects;
    public $search = '';
    public $status = '';
    public $priority = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

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
        return view('700-page-pms-project-list/000-index');
    }
}