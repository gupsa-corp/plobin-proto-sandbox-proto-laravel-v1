<?php

namespace App\Services\Rfx\FormExecution\DownloadResult;

class Service
{
    public function execute($executionId): array
    {
        return [
            'success' => true,
            'downloadUrl' => "/downloads/execution_result_{$executionId}.xlsx",
            'message' => '결과 파일을 다운로드했습니다.'
        ];
    }
}