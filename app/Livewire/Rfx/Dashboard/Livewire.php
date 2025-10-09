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
        $this->stats = [
            'totalFiles' => 156,
            'analyzedFiles' => 89,
            'pendingAnalysis' => 23,
            'errorCount' => 3
        ];

        $this->recentFiles = [
            [
                'name' => '프로젝트_계획서.pdf',
                'status' => 'completed',
                'uploadedAt' => '10분 전'
            ],
            [
                'name' => '데이터_분석.xlsx',
                'status' => 'analyzing',
                'uploadedAt' => '25분 전'
            ]
        ];

        $this->analysisQueue = [
            [
                'id' => 1,
                'fileName' => '회의록_20241009.docx',
                'priority' => 'high',
                'estimatedTime' => '5분'
            ],
            [
                'id' => 2,
                'fileName' => '기술문서.pdf',
                'priority' => 'medium',
                'estimatedTime' => '8분'
            ]
        ];
    }

    public function render()
    {
        return view('700-page-rfx-dashboard/000-index');
    }
}