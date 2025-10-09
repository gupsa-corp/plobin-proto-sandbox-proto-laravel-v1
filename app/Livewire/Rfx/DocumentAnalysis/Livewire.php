<?php

namespace App\Livewire\Rfx\DocumentAnalysis;

use Livewire\Component;
use App\Services\Rfx\DocumentAnalysis\Service;

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
        $service = new Service();
        $this->selectedDocument = collect($this->documents)->firstWhere('id', $documentId);
        $this->analysisResult = $service->getAnalysisResult($documentId);
    }

    public function regenerateAnalysis($documentId)
    {
        $service = new Service();
        $result = $service->regenerateAnalysis($documentId);
        
        if ($result['success']) {
            session()->flash('message', '분석을 다시 시작했습니다.');
            $this->loadDocuments();
        }
    }

    public function exportAnalysis($documentId, $format)
    {
        $service = new Service();
        $result = $service->exportAnalysis($documentId, $format);
        
        if ($result['success']) {
            session()->flash('message', "분석 결과를 {$format} 형식으로 내보냈습니다.");
        }
    }

    public function loadDocuments()
    {
        $service = new Service();
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