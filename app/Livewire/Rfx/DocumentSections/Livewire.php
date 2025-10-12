<?php

namespace App\Livewire\Rfx\DocumentSections;

use Livewire\Component;
use App\Services\Rfx\DocumentSections\GetList\Service as GetListService;
use App\Services\Rfx\DocumentSections\GetDetail\Service as GetDetailService;

class Livewire extends Component
{
    public $documentId;
    public $documentName = '';
    public $pageNumber = 1;
    public $sections = [];
    public $selectedSection = null;
    public $sectionBlocks = [];
    public $statistics = [];

    // UI 상태
    public $expandedSections = [];

    public function mount($documentId)
    {
        $this->documentId = $documentId;
        $this->loadSections();
    }

    public function loadSections()
    {
        $service = new GetListService();
        $result = $service->execute($this->documentId, $this->pageNumber);

        if ($result['success']) {
            $this->sections = $result['data']['sections'];
            $this->documentName = $result['data']['document_name'];
            $this->statistics = $result['data']['statistics'];
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function selectSection($sectionId)
    {
        $service = new GetDetailService();
        $result = $service->execute($this->documentId, $sectionId, $this->pageNumber);

        if ($result['success']) {
            $this->selectedSection = $result['data']['section'];
            $this->sectionBlocks = $result['data']['section']['blocks'] ?? [];
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function toggleSection($sectionId)
    {
        $index = array_search($sectionId, $this->expandedSections);

        if ($index !== false) {
            // 이미 펼쳐져 있으면 접기
            unset($this->expandedSections[$index]);
            $this->expandedSections = array_values($this->expandedSections);
        } else {
            // 접혀 있으면 펼치기
            $this->expandedSections[] = $sectionId;
        }
    }

    public function isSectionExpanded($sectionId): bool
    {
        return in_array($sectionId, $this->expandedSections);
    }

    public function changePage($page)
    {
        $this->pageNumber = $page;
        $this->loadSections();
        $this->selectedSection = null;
        $this->sectionBlocks = [];
    }

    public function render()
    {
        return view('700-page-rfx-document-sections/000-index')
            ->layout('300-layout-common/000-app');
    }
}
