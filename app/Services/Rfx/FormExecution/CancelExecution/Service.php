<?php

namespace App\Services\Rfx\FormExecution\CancelExecution;

class Service
{
    public function execute($executionId): array
    {
        return [
            'success' => true,
            'message' => '폼 실행이 취소되었습니다.'
        ];
    }
}