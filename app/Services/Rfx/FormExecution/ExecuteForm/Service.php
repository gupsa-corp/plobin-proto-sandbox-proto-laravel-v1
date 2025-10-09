<?php

namespace App\Services\Rfx\FormExecution\ExecuteForm;

class Service
{
    public function execute($formId, $formData): array
    {
        return [
            'success' => true,
            'data' => [
                'executionId' => rand(100, 999),
                'status' => 'pending',
                'message' => '폼 실행이 시작되었습니다.'
            ]
        ];
    }
}