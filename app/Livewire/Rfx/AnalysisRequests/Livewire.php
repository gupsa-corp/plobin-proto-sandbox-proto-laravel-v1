<?php

namespace App\Livewire\Rfx\AnalysisRequests;

use Livewire\Component;
use App\Services\Rfx\AnalysisRequests\List\Service as ListService;
use App\Services\Rfx\AnalysisRequests\Create\Service as CreateService;
use App\Services\Rfx\AnalysisRequests\UpdateStatus\Service as UpdateStatusService;
use App\Services\Rfx\AnalysisRequests\UpdatePriority\Service as UpdatePriorityService;
use App\Services\Rfx\AnalysisRequests\Assign\Service as AssignService;
use App\Services\Rfx\AnalysisRequests\Delete\Service as DeleteService;

class Livewire extends Component
{
    public $requests;
    public $selectedRequest = null;
    public $statusFilter = '';
    public $priorityFilter = '';
    public $dateFilter = '';
    public $showCreateModal = false;
    public $newRequest = [
        'title' => '',
        'description' => '',
        'priority' => 'medium',
        'requiredBy' => '',
        'documentIds' => []
    ];

    public function mount()
    {
        $this->loadRequests();
    }

    public function updatedStatusFilter()
    {
        $this->loadRequests();
    }

    public function updatedPriorityFilter()
    {
        $this->loadRequests();
    }

    public function updatedDateFilter()
    {
        $this->loadRequests();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->newRequest = [
            'title' => '',
            'description' => '',
            'priority' => 'medium',
            'requiredBy' => '',
            'documentIds' => []
        ];
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function createRequest()
    {
        $service = new CreateService();
        $result = $service->execute($this->newRequest);
        
        if ($result['success']) {
            session()->flash('message', '분석 요청이 생성되었습니다.');
            $this->closeCreateModal();
            $this->loadRequests();
        }
    }

    public function selectRequest($requestId)
    {
        $this->selectedRequest = collect($this->requests)->firstWhere('id', $requestId);
    }

    public function updateRequestStatus($requestId, $status)
    {
        $service = new UpdateStatusService();
        $result = $service->execute($requestId, $status);
        
        if ($result['success']) {
            session()->flash('message', '요청 상태가 업데이트되었습니다.');
            $this->loadRequests();
        }
    }

    public function updateRequestPriority($requestId, $priority)
    {
        $service = new UpdatePriorityService();
        $result = $service->execute($requestId, $priority);
        
        if ($result['success']) {
            session()->flash('message', '요청 우선순위가 업데이트되었습니다.');
            $this->loadRequests();
        }
    }

    public function assignRequest($requestId, $assignee)
    {
        $service = new AssignService();
        $result = $service->execute($requestId, $assignee);
        
        if ($result['success']) {
            session()->flash('message', '요청이 담당자에게 배정되었습니다.');
            $this->loadRequests();
        }
    }

    public function deleteRequest($requestId)
    {
        $service = new DeleteService();
        $result = $service->execute($requestId);
        
        if ($result['success']) {
            session()->flash('message', '요청이 삭제되었습니다.');
            $this->loadRequests();
            $this->selectedRequest = null;
        }
    }

    public function loadRequests()
    {
        $service = new ListService();
        $this->requests = $service->execute([
            'status' => $this->statusFilter,
            'priority' => $this->priorityFilter,
            'date' => $this->dateFilter
        ]);
    }

    public function render()
    {
        return view('700-page-rfx-analysis-requests/000-index');
    }
}