<?php

namespace App\Livewire\Rfx\DocumentBlocks;

use Livewire\Component;
use App\Services\Rfx\DocumentBlocks\GetList\Service as GetListService;
use App\Services\Rfx\DocumentBlocks\GetDetail\Service as GetDetailService;
use App\Services\Rfx\DocumentBlocks\Update\Service as UpdateService;

class Livewire extends Component
{
    public $documentId;
    public $documentName = '';
    public $pageNumber = 1;
    public $blocks = [];
    public $selectedBlock = null;
    public $statistics = [];
    public $pagination = [];

    // 필터
    public $blockTypeFilter = '';
    public $confidenceMin = 0.0;
    public $limit = 20;

    // UI 상태
    public $showImageModal = false;
    public $selectedBlockImage = null;
    public $showEditModal = false;
    public $editingBlock = null;

    // 편집 폼 데이터
    public $editText = '';
    public $editBlockType = '';
    public $editConfidence = 0.0;

    public function mount($documentId)
    {
        $this->documentId = $documentId;
        $this->loadBlocks();
    }

    public function loadBlocks()
    {
        $service = new GetListService();
        $result = $service->execute([
            'document_id' => $this->documentId,
            'page' => $this->pageNumber,
            'block_type' => $this->blockTypeFilter ?: null,
            'confidence_min' => $this->confidenceMin > 0 ? $this->confidenceMin : null,
            'limit' => $this->limit,
        ]);

        if ($result['success']) {
            $this->blocks = $result['data']['blocks'];
            $this->documentName = $result['data']['document_name'];
            $this->statistics = $result['data']['statistics'];
            $this->pagination = $result['data']['pagination'];
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function selectBlock($blockId)
    {
        $service = new GetDetailService();
        $result = $service->execute($this->documentId, $blockId, $this->pageNumber);

        if ($result['success']) {
            $this->selectedBlock = $result['data']['block'];
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function applyFilter()
    {
        $this->pageNumber = 1; // 필터 적용 시 첫 페이지로
        $this->loadBlocks();
    }

    public function changePage($page)
    {
        $this->pageNumber = $page;
        $this->loadBlocks();
    }

    public function showBlockImage($blockId)
    {
        $this->selectedBlockImage = "/api/rfx/documents/{$this->documentId}/blocks/{$blockId}/image?page={$this->pageNumber}";
        $this->showImageModal = true;
    }

    public function closeImageModal()
    {
        $this->showImageModal = false;
        $this->selectedBlockImage = null;
    }

    public function openEditModal($blockId)
    {
        // 블록 정보 로드
        $block = collect($this->blocks)->firstWhere('block_id', $blockId);

        if ($block) {
            $this->editingBlock = $block;
            $this->editText = $block['text'];
            $this->editBlockType = $block['block_type'];
            $this->editConfidence = $block['confidence'];
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingBlock = null;
        $this->editText = '';
        $this->editBlockType = '';
        $this->editConfidence = 0.0;
    }

    public function saveBlock()
    {
        if (!$this->editingBlock) {
            return;
        }

        $service = new UpdateService();
        $result = $service->execute(
            $this->documentId,
            $this->editingBlock['block_id'],
            [
                'text' => $this->editText,
                'block_type' => $this->editBlockType,
                'confidence' => $this->editConfidence,
            ],
            $this->pageNumber
        );

        if ($result['success']) {
            session()->flash('message', '블록이 성공적으로 수정되었습니다');
            $this->closeEditModal();
            $this->loadBlocks();

            // 선택된 블록이 수정된 블록이면 업데이트
            if ($this->selectedBlock && $this->selectedBlock['block_id'] === $this->editingBlock['block_id']) {
                $this->selectBlock($this->editingBlock['block_id']);
            }
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function updatedBlockTypeFilter()
    {
        $this->applyFilter();
    }

    public function updatedConfidenceMin()
    {
        $this->applyFilter();
    }

    public function render()
    {
        return view('700-page-rfx-document-blocks/000-index')
            ->layout('300-layout-common/000-app');
    }
}
