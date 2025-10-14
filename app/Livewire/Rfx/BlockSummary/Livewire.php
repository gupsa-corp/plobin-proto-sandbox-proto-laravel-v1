<?php

namespace App\Livewire\Rfx\BlockSummary;

use Livewire\Component;
use App\Services\Rfx\BlockSummary\GetDocuments\Service as GetDocumentsService;
use App\Services\Rfx\BlockSummary\GetBlocks\Service as GetBlocksService;
use App\Services\Rfx\BlockSummary\GenerateSummary\Service as GenerateSummaryService;

class Livewire extends Component
{
    public $documents = [];
    public $selectedDocument = null;
    public $blocks = [];
    public $summary = null;
    public $isGenerating = false;
    public $search = '';
    public $statusFilter = '';

    public function mount()
    {
        $this->loadDocuments();
    }

    public function loadDocuments()
    {
        $service = new GetDocumentsService();
        $this->documents = $service->execute([
            'search' => $this->search,
            'status' => $this->statusFilter,
        ]);
    }

    public function selectDocument($documentId)
    {
        $this->selectedDocument = collect($this->documents)->firstWhere('id', $documentId);
        $this->blocks = [];
        $this->summary = null;

        // 선택된 문서의 블록 로드
        $service = new GetBlocksService();
        $result = $service->execute($documentId);

        if ($result['success']) {
            $this->blocks = $result['data']['blocks'];
        } else {
            session()->flash('error', $result['message']);
        }

        // URL 업데이트
        $this->js("window.history.pushState({}, '', '/rfx/block-summary/{$documentId}')");
    }

    public function generateSummary()
    {
        if (!$this->selectedDocument || empty($this->blocks)) {
            session()->flash('error', '문서를 선택하고 블록 데이터가 있어야 합니다');
            return;
        }

        $this->isGenerating = true;

        $service = new GenerateSummaryService();
        $result = $service->execute([
            'document_id' => $this->selectedDocument['id'],
            'blocks' => $this->blocks,
        ]);

        $this->isGenerating = false;

        if ($result['success']) {
            $this->summary = $result['data'];
            session()->flash('message', '요약이 성공적으로 생성되었습니다');
        } else {
            session()->flash('error', $result['message']);
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

    public function render()
    {
        return view('700-page-rfx-block-summary/000-index')
            ->layout('300-layout-common/000-app');
    }
}
