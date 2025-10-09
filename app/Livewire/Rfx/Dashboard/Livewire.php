<?php

namespace App\Livewire\Rfx\Dashboard;

use Livewire\Component;

class Livewire extends Component
{
    public $stats;
    public $recentFiles;
    public $analysisQueue;

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // 통계 데이터 로드
        $this->stats = [
            'totalFiles' => \App\Models\Plobin\UploadedFile::count(),
            'analyzedFiles' => \App\Models\Plobin\UploadedFile::where('status', 'completed')->count(),
            'pendingAnalysis' => \App\Models\Plobin\UploadedFile::where('status', 'analyzing')->count(),
            'errorCount' => \App\Models\Plobin\UploadedFile::where('status', 'error')->count()
        ];

        // 최근 업로드된 파일들
        $recentFiles = \App\Models\Plobin\UploadedFile::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->recentFiles = $recentFiles->map(function($file) {
            return [
                'name' => $file->original_name,
                'status' => $file->status,
                'uploadedAt' => $file->created_at->diffForHumans()
            ];
        })->toArray();

        // 분석 대기열 (분석 중이거나 대기 중인 파일들)
        $queueFiles = \App\Models\Plobin\UploadedFile::whereIn('status', ['uploaded', 'analyzing'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        $this->analysisQueue = $queueFiles->map(function($file, $index) {
            return [
                'id' => $file->id,
                'fileName' => $file->original_name,
                'priority' => $this->getEstimatedPriority($file),
                'estimatedTime' => $this->getEstimatedTime($file)
            ];
        })->toArray();
    }

    private function getEstimatedPriority($file): string
    {
        // 파일 크기와 타입을 기반으로 우선순위 추정
        if ($file->file_size > 10 * 1024 * 1024) { // 10MB 이상
            return 'low';
        } elseif (in_array($file->mime_type, ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'high';
        }
        return 'medium';
    }

    private function getEstimatedTime($file): string
    {
        // 파일 크기를 기반으로 예상 처리 시간 계산
        $sizeInMB = $file->file_size / (1024 * 1024);
        $minutes = max(1, ceil($sizeInMB * 2)); // 1MB당 약 2분 가정
        return $minutes . '분';
    }

    public function render()
    {
        return view('700-page-rfx-dashboard/000-index');
    }
}