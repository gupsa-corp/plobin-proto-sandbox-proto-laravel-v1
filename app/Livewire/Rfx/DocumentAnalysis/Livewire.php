<?php

namespace App\Livewire\Rfx\DocumentAnalysis;

use Livewire\Component;
use App\Services\Rfx\DocumentAnalysis\List\Service as ListService;
use App\Services\Rfx\DocumentAnalysis\GetResult\Service as GetResultService;
use App\Services\Rfx\DocumentAnalysis\Regenerate\Service as RegenerateService;
use App\Services\Rfx\DocumentAnalysis\Export\Service as ExportService;
use App\Services\Rfx\DocumentAnalysis\DownloadOriginal\Service as DownloadOriginalService;
use App\Services\Rfx\DocumentAnalysis\DownloadVisualization\Service as DownloadVisualizationService;
use App\Services\Rfx\AnalysisSnapshot\GetSnapshots\Service as GetSnapshotsService;
use App\Services\Rfx\AnalysisSnapshot\GetSnapshotDetail\Service as GetSnapshotDetailService;

class Livewire extends Component
{
    public $documents;
    public $selectedDocument = null;
    public $analysisResult = null;
    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $documentId = null;
    public $snapshots = [];
    public $selectedSnapshotId = null;
    public $viewingSnapshot = false;

    public function mount($documentId = null)
    {
        $this->documentId = $documentId;
        $this->loadDocuments();

        // URL에서 documentId가 전달된 경우 해당 문서 자동 선택
        if ($this->documentId) {
            $this->selectDocument($this->documentId);
        }
    }

    public function updatedSearch()
    {
        $this->loadDocuments();
    }

    public function updatedStatusFilter()
    {
        $this->loadDocuments();
    }

    public function updatedDateFilter()
    {
        $this->loadDocuments();
    }

    public function selectDocument($documentId)
    {
        $service = new GetResultService();
        $this->selectedDocument = collect($this->documents)->firstWhere('id', $documentId);
        $this->analysisResult = $service->execute($documentId);

        // 스냅샷 로드
        $this->loadSnapshots($documentId);
        $this->viewingSnapshot = false;
        $this->selectedSnapshotId = null;

        // URL 업데이트 (브라우저 히스토리에 추가)
        $this->js("window.history.pushState({}, '', '/rfx/analysis/{$documentId}')");
    }

    public function loadSnapshots($documentId)
    {
        $service = new GetSnapshotsService();
        $this->snapshots = $service->execute($documentId);
    }

    public function selectSnapshot($snapshotId)
    {
        $service = new GetSnapshotDetailService();
        $this->analysisResult = $service->execute($snapshotId);
        $this->selectedSnapshotId = $snapshotId;
        $this->viewingSnapshot = true;
    }

    public function viewLatest()
    {
        $this->viewingSnapshot = false;
        $this->selectedSnapshotId = null;
        if ($this->selectedDocument) {
            $this->selectDocument($this->selectedDocument['id']);
        }
    }

    public function regenerateAnalysis($documentId)
    {
        $service = new RegenerateService();
        $result = $service->execute($documentId);

        if ($result['success']) {
            session()->flash('message', $result['message']);
            $this->loadDocuments();

            // 선택된 문서 재로드 및 스냅샷 목록 새로고침
            if ($this->selectedDocument && $this->selectedDocument['id'] === $documentId) {
                $this->selectDocument($documentId);
            }
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function exportAnalysis($documentId, $format)
    {
        $service = new ExportService();
        $result = $service->execute($documentId, $format);

        if ($result['success']) {
            session()->flash('message', "분석 결과를 {$format} 형식으로 내보냈습니다.");
        }
    }

    public function downloadOriginal($documentId, $pageNumber = 1)
    {
        $service = new DownloadOriginalService();
        return $service->execute($documentId, $pageNumber);
    }

    public function downloadVisualization($documentId, $pageNumber = 1)
    {
        $service = new DownloadVisualizationService();
        return $service->execute($documentId, $pageNumber);
    }

    public function loadDocuments()
    {
        $service = new ListService();
        $this->documents = $service->execute([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'date' => $this->dateFilter
        ]);
    }

    public function render()
    {
        return view('700-page-rfx-document-analysis/000-index');
    }
}