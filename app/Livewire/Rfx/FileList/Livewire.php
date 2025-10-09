<?php

namespace App\Livewire\Rfx\FileList;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Rfx\FileManager\Service;

class Livewire extends Component
{
    use WithPagination;

    public $files;
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $sortBy = 'uploadedAt';
    public $sortDirection = 'desc';
    public $selectedFile = null;

    public function mount()
    {
        $this->loadFiles();
    }

    public function updatedSearch()
    {
        $this->loadFiles();
    }

    public function updatedStatusFilter()
    {
        $this->loadFiles();
    }

    public function updatedTypeFilter()
    {
        $this->loadFiles();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadFiles();
    }

    public function selectFile($fileId)
    {
        $this->selectedFile = collect($this->files)->firstWhere('id', $fileId);
    }

    public function analyzeFile($fileId)
    {
        $service = new Service();
        $result = $service->analyzeFile($fileId);
        
        if ($result['success']) {
            session()->flash('message', '분석이 시작되었습니다.');
            $this->loadFiles();
        }
    }

    public function deleteFile($fileId)
    {
        $service = new Service();
        $result = $service->deleteFile($fileId);
        
        if ($result['success']) {
            session()->flash('message', '파일이 삭제되었습니다.');
            $this->loadFiles();
        }
    }

    public function loadFiles()
    {
        $service = new Service();
        $this->files = $service->getFiles([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'type' => $this->typeFilter,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection
        ]);
    }

    public function render()
    {
        return view('700-page-rfx-file-list/000-index');
    }
}