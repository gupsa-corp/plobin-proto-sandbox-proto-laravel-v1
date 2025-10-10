<?php

namespace App\Livewire\Rfx\DocumentAssets;

use Livewire\Component;
use App\Services\Rfx\DocumentAsset\GetAssets\Service;
use App\Services\Rfx\DocumentAsset\UpdateAssetContent\Service as UpdateContentService;
use App\Services\Rfx\DocumentAsset\UpdateAssetSummary\Service as UpdateSummaryService;
use App\Services\Rfx\DocumentAsset\UpdateHelpfulContent\Service as UpdateHelpfulService;
use App\Services\Rfx\DocumentAsset\ToggleAssetStatus\Service as ToggleStatusService;
use App\Services\Rfx\DocumentAsset\GenerateAssetSummary\Service as GenerateAssetSummaryService;
use App\Services\Rfx\DocumentAsset\RegenerateSummary\Service as RegenerateSummaryService;

class Livewire extends Component
{
    public $analysisRequestId;
    public $assets = [];
    public $requestInfo = [];

    // 편집 모드
    public $editingAssetId = null;
    public $editingField = ''; // 'content', 'summary', 'helpful'
    public $editingValue = '';
    public $showEditModal = false;

    // 버전 선택
    public $selectedVersions = [];

    public function mount($requestId)
    {
        $this->analysisRequestId = $requestId;
        $this->loadData();
    }

    public function loadData()
    {
        // 분석 요청 정보 조회
        $request = \DB::table('rfx_ai_analysis_requests')
            ->where('id', $this->analysisRequestId)
            ->first();

        if ($request) {
            $this->requestInfo = [
                'id' => $request->id,
                'file_name' => $request->file_name,
                'file_type' => $request->file_type,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'completed_at' => $request->completed_at,
            ];
        }

        // 섹션 목록 조회
        $service = new Service();
        $result = $service->execute($this->analysisRequestId);

        if ($result['success']) {
            $this->assets = $result['data'];

            // 기본 버전을 현재 버전으로 설정
            foreach ($this->assets as $asset) {
                if (isset($asset['summary'])) {
                    $this->selectedVersions[$asset['id']] = $asset['summary']['current_version_timestamp'];
                }
            }
        }
    }

    public function toggleAssetStatus($assetId)
    {
        $service = new ToggleStatusService();
        $result = $service->execute($assetId);

        if ($result['success']) {
            $this->loadData();
            $this->dispatch('status-updated', status: $result['new_status']);
        }
    }

    public function openEditModal($assetId, $field)
    {
        $asset = collect($this->assets)->firstWhere('id', $assetId);

        if (!$asset) {
            return;
        }

        $this->editingAssetId = $assetId;
        $this->editingField = $field;

        switch ($field) {
            case 'content':
                $this->editingValue = $asset['content'];
                break;
            case 'summary':
                $this->editingValue = $asset['summary']['ai_summary'] ?? '';
                break;
            case 'helpful':
                $this->editingValue = $asset['summary']['helpful_content'] ?? '';
                break;
        }

        $this->showEditModal = true;
    }

    public function saveEdit()
    {
        if (!$this->editingAssetId || !$this->editingField) {
            return;
        }

        $result = ['success' => false];

        switch ($this->editingField) {
            case 'content':
                $service = new UpdateContentService();
                $result = $service->execute($this->editingAssetId, $this->editingValue);
                break;
            case 'summary':
                $service = new UpdateSummaryService();
                $result = $service->execute($this->editingAssetId, $this->editingValue);
                break;
            case 'helpful':
                $service = new UpdateHelpfulService();
                $result = $service->execute($this->editingAssetId, $this->editingValue);
                break;
        }

        if ($result['success']) {
            $this->showEditModal = false;
            $this->loadData();
            $this->dispatch('edit-saved');
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingAssetId = null;
        $this->editingField = '';
        $this->editingValue = '';
    }

    public function switchVersion($assetId, $versionTimestamp)
    {
        // 선택된 버전의 내용을 표시
        $this->selectedVersions[$assetId] = $versionTimestamp;

        // 해당 asset 찾기
        $assetIndex = collect($this->assets)->search(function($asset) use ($assetId) {
            return $asset['id'] === $assetId;
        });

        if ($assetIndex !== false && isset($this->assets[$assetIndex]['summary'])) {
            // 선택된 버전 찾기
            $versions = $this->assets[$assetIndex]['summary']['versions'];
            $selectedVersion = collect($versions)->firstWhere('version_timestamp', $versionTimestamp);

            if ($selectedVersion) {
                // 표시되는 요약과 도움 내용 업데이트
                $this->assets[$assetIndex]['summary']['ai_summary'] = $selectedVersion['ai_summary'];
                $this->assets[$assetIndex]['summary']['helpful_content'] = $selectedVersion['helpful_content'];
            }
        }
    }

    public function generateSummary($assetId)
    {
        try {
            $service = new GenerateAssetSummaryService();
            $result = $service->execute($assetId);

            if ($result['success']) {
                // 데이터 새로고침
                $this->loadData();
                session()->flash('success', 'AI 요약이 성공적으로 생성되었습니다.');
            } else {
                session()->flash('error', $result['error'] ?? 'AI 요약 생성에 실패했습니다.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'AI 요약 생성 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    public function regenerateSummary($assetId)
    {
        try {
            $service = new RegenerateSummaryService();
            $result = $service->execute($assetId);

            if ($result['success']) {
                // 데이터 새로고침
                $this->loadData();
                session()->flash('success', 'AI 요약이 성공적으로 재분석되었습니다. 새로운 버전이 생성되었습니다.');
            } else {
                session()->flash('error', $result['error'] ?? 'AI 요약 재분석에 실패했습니다.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'AI 요약 재분석 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('700-page-rfx-document-assets.000-index');
    }
}
