<?php

namespace App\Livewire\Rfx\AiAnalysis;

use Livewire\Component;
use App\Services\Rfx\AiAnalysis\GetRequests\Service as GetRequestsService;
use App\Services\Rfx\AiAnalysis\GetRequestDetail\Service as GetRequestDetailService;
use App\Services\Rfx\AiAnalysis\RetryRequest\Service as RetryRequestService;
use App\Services\Rfx\AiAnalysis\DownloadResult\Service as DownloadResultService;
use App\Services\Rfx\AiAnalysis\GenerateSummary\Service as GenerateSummaryService;

class Livewire extends Component
{
    public $requests = [];
    public $stats = [];
    public $statusFilter = '';
    public $analysisTypeFilter = '';
    public $dateRangeFilter = '';
    public $autoRefresh = false;
    public $selectedRequest = null;
    public $summary = null;
    public $showSummary = false;
    public $showDetailModal = false;

    public function mount()
    {
        $this->loadRequests();
    }

    public function updatedStatusFilter()
    {
        $this->loadRequests();
    }

    public function updatedAnalysisTypeFilter()
    {
        $this->loadRequests();
    }

    public function updatedDateRangeFilter()
    {
        $this->loadRequests();
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function viewDetails($requestId)
    {
        if ($requestId === null) {
            $this->selectedRequest = null;
            $this->summary = null;
            $this->showSummary = false;
            $this->showDetailModal = false;
            return;
        }

        $service = new GetRequestDetailService();
        $result = $service->execute($requestId);

        if ($result['success']) {
            $this->selectedRequest = $result['data'];
            $this->showSummary = false;
            $this->showDetailModal = true;
        }
    }

    public function viewSummary($requestId)
    {
        $service = new GenerateSummaryService();
        $result = $service->execute($requestId);

        if ($result['success']) {
            $this->summary = $result['data'];
            $this->showSummary = true;
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function closeSummary()
    {
        $this->showSummary = false;
        $this->summary = null;
    }

    public function retryAnalysis($requestId)
    {
        $service = new RetryRequestService();
        $result = $service->execute($requestId);

        if ($result['success']) {
            session()->flash('message', '분석 요청이 재시도되었습니다.');
            $this->loadRequests();
            $this->selectedRequest = null;
        } else {
            session()->flash('error', '재시도에 실패했습니다.');
        }
    }

    public function downloadResult($requestId)
    {
        $service = new DownloadResultService();
        $result = $service->execute($requestId);

        if ($result['success']) {
            // Livewire에서는 response()->download()를 직접 반환할 수 없으므로
            // JavaScript를 통해 데이터를 다운로드하도록 이벤트 디스패치
            $this->dispatch('download-file', [
                'content' => $result['content'],
                'filename' => $result['fileName']
            ]);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function loadRequests()
    {
        $service = new GetRequestsService();
        $result = $service->execute([
            'status' => $this->statusFilter,
            'analysisType' => $this->analysisTypeFilter,
            'dateRange' => $this->dateRangeFilter,
        ]);

        $this->requests = $result['requests'] ?? [];
        $this->stats = $result['stats'] ?? [];
    }

    public function render()
    {
        return view('700-page-rfx-ai-analysis/000-index')
            ->layout('components.layouts.app');
    }
}
