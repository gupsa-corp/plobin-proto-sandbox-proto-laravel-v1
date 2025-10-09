<?php

namespace App\Services\Rfx\FormExecution\RetryExecution;

class Service
{
    public function execute($executionId): array
    {
        return [
            'success' => true,
            'message' => '폼 실행을 재시작했습니다.'
        ];
    }
}