<?php

namespace App\Livewire\Rfx\DocumentAnalysis;

use Livewire\Component;
use App\Services\Rfx\DocumentAnalysis\List\Service as ListService;
use App\Services\Rfx\DocumentAnalysis\GetResult\Service as GetResultService;
use App\Services\Rfx\DocumentAnalysis\Regenerate\Service as RegenerateService;
use App\Services\Rfx\DocumentAnalysis\Export\Service as ExportService;

class Livewire extends Component
{
    public $documents;
    public $selectedDocument = null;
    public $analysisResult = null;
    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';

    public function mount()
    {
        $this->loadDocuments();
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
    }

    public function regenerateAnalysis($documentId)
    {
        $service = new RegenerateService();
        $result = $service->execute($documentId);
        
        if ($result['success']) {
            session()->flash('message', '분석을 다시 시작했습니다.');
            $this->loadDocuments();
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