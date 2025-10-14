<?php

namespace App\Livewire\Rfx\BlockSummary;

use Livewire\Component;
use App\Services\Rfx\BlockSummary\GetDocuments\Service as GetDocumentsService;
use App\Services\Rfx\BlockSummary\GetBlocks\Service as GetBlocksService;
use App\Services\Rfx\BlockSummary\GenerateSummary\Service as GenerateSummaryService;
use App\Services\Rfx\BlockSummary\GetPageBlocks\Service as GetPageBlocksService;
use App\Services\Rfx\BlockSummary\SaveSummary\Service as SaveSummaryService;
use App\Services\Rfx\BlockSummary\SavePageSummary\Service as SavePageSummaryService;
use App\Services\Rfx\BlockSummary\SaveSectionAnalysis\Service as SaveSectionAnalysisService;
use App\Services\Rfx\BlockSummary\GetSavedSummary\Service as GetSavedSummaryService;
use App\Services\Rfx\BlockSummary\UpdateSectionVersion\Service as UpdateSectionVersionService;

class Livewire extends Component
{
    public $documents = [];
    public $selectedDocument = null;
    public $blocks = [];
    public $pageGroups = []; // 페이지별 블록 그룹
    public $summary = null;
    public $isGenerating = false;
    public $search = '';
    public $statusFilter = '';
    public $savedSummary = null; // 저장된 요약 데이터
    public $editingContent = []; // 수정 중인 교정 내용
    public $selectedVersions = []; // 섹션별 선택된 버전 ID

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
        $this->pageGroups = [];
        $this->summary = null;
        $this->savedSummary = null;

        // 저장된 요약 데이터 확인
        $savedService = new GetSavedSummaryService();
        $savedResult = $savedService->execute(['document_id' => $documentId]);

        if ($savedResult['success']) {
            $this->savedSummary = $savedResult['data'];
            // 저장된 데이터가 있으면 바로 표시
            session()->flash('message', '저장된 분석 결과를 불러왔습니다');
            return;
        }

        // 선택된 문서의 블록 로드
        $service = new GetBlocksService();
        $result = $service->execute($documentId);

        if ($result['success']) {
            $this->blocks = $result['data']['blocks'];

            // 블록을 페이지별로 그룹화
            $pageBlocksService = new GetPageBlocksService();
            $this->pageGroups = $pageBlocksService->execute($this->blocks);
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

        // 1. 전체 요약 생성
        $summaryService = new GenerateSummaryService();
        $summaryResult = $summaryService->execute([
            'document_id' => $this->selectedDocument['id'],
            'blocks' => $this->blocks,
        ]);

        if (!$summaryResult['success']) {
            $this->isGenerating = false;
            session()->flash('error', $summaryResult['message']);
            return;
        }

        $this->summary = $summaryResult['data'];

        // 2. DocumentSummary 저장
        $saveService = new SaveSummaryService();
        $saveResult = $saveService->execute([
            'document_id' => $this->selectedDocument['id'],
            'total_pages' => $this->selectedDocument['total_pages'] ?? count($this->pageGroups),
            'total_blocks' => count($this->blocks),
        ]);

        if (!$saveResult['success']) {
            $this->isGenerating = false;
            session()->flash('error', $saveResult['message']);
            return;
        }

        $documentSummary = $saveResult['data'];

        // 3. 페이지별 요약 저장
        foreach ($this->pageGroups as $pageGroup) {
            $pageSummaryService = new SavePageSummaryService();
            $pageSummaryResult = $pageSummaryService->execute([
                'document_summary_id' => $documentSummary->id,
                'document_id' => $this->selectedDocument['id'],
                'page_number' => $pageGroup['page_number'],
                'blocks' => $pageGroup['blocks'],
            ]);

            if ($pageSummaryResult['success']) {
                $pageSummary = $pageSummaryResult['data'];

                // 4. 각 블록에 대해 섹션 분석 저장
                foreach ($pageGroup['blocks'] as $index => $block) {
                    $sectionService = new SaveSectionAnalysisService();
                    $sectionService->execute([
                        'page_summary_id' => $pageSummary->id,
                        'block' => $block,
                        'block_index' => $index,
                        'ai_summary' => $this->summary['summary'] ?? '',
                        'helpful_content' => null,
                        'asset_type' => 'general',
                        'asset_type_name' => '일반',
                        'asset_type_icon' => '📄',
                    ]);
                }
            }
        }

        $this->isGenerating = false;
        session()->flash('message', '요약이 성공적으로 생성되고 저장되었습니다');

        // 저장된 데이터 다시 로드
        $this->selectDocument($this->selectedDocument['id']);
    }

    public function updatedSearch()
    {
        $this->loadDocuments();
    }

    public function updatedStatusFilter()
    {
        $this->loadDocuments();
    }

    public function saveCorrection($sectionId)
    {
        if (!isset($this->editingContent[$sectionId])) {
            session()->flash('error', '수정된 내용이 없습니다');
            return;
        }

        $service = new UpdateSectionVersionService();
        $result = $service->execute([
            'section_id' => $sectionId,
            'new_summary' => $this->editingContent[$sectionId],
            'user_id' => auth()->id() ?? 'user',
        ]);

        if ($result['success']) {
            session()->flash('message', '교정 내용이 저장되었습니다 (기본)');
            // 저장된 데이터 다시 로드
            $this->selectDocument($this->selectedDocument['id']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function setDefaultVersion($sectionId, $versionId)
    {
        $service = new \App\Services\Rfx\BlockSummary\SetDefaultVersion\Service();
        $result = $service->execute([
            'section_id' => $sectionId,
            'version_id' => $versionId,
        ]);

        if ($result['success']) {
            session()->flash('message', '선택한 버전이 기본값으로 설정되었습니다');
            // 저장된 데이터 다시 로드
            $this->selectDocument($this->selectedDocument['id']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function render()
    {
        return view('700-page-rfx-block-summary/000-index')
            ->layout('300-layout-common/000-app');
    }
}
