<?php

namespace App\Services\Rfx\FormExecution\GetForms;

class Service
{
    public function execute(array $filters = []): array
    {
        $forms = [
            [
                'id' => 1,
                'name' => '계약서 데이터 추출',
                'description' => '계약서에서 주요 데이터를 자동으로 추출합니다.',
                'type' => 'extraction',
                'category' => '법무',
                'version' => '1.2',
                'lastUpdated' => '2024-10-01',
                'fieldsCount' => 12,
                'executionCount' => 45
            ],
            [
                'id' => 2,
                'name' => '송장 데이터 검증',
                'description' => '송장의 정확성과 일관성을 검증합니다.',
                'type' => 'validation',
                'category' => '회계',
                'version' => '2.0',
                'lastUpdated' => '2024-09-25',
                'fieldsCount' => 8,
                'executionCount' => 32
            ],
            [
                'id' => 3,
                'name' => '회의록 요약 생성',
                'description' => '회의록에서 핵심 내용과 액션 아이템을 추출합니다.',
                'type' => 'summary',
                'category' => '관리',
                'version' => '1.5',
                'lastUpdated' => '2024-10-05',
                'fieldsCount' => 6,
                'executionCount' => 78
            ],
            [
                'id' => 4,
                'name' => '재무제표 분석',
                'description' => '재무제표의 주요 지표를 분석하고 리포트를 생성합니다.',
                'type' => 'analysis',
                'category' => '재무',
                'version' => '1.8',
                'lastUpdated' => '2024-09-30',
                'fieldsCount' => 15,
                'executionCount' => 23
            ],
            [
                'id' => 5,
                'name' => '품질 보고서 검토',
                'description' => '품질 관리 문서의 규정 준수 여부를 검토합니다.',
                'type' => 'review',
                'category' => '품질',
                'version' => '1.1',
                'lastUpdated' => '2024-09-20',
                'fieldsCount' => 9,
                'executionCount' => 15
            ]
        ];

        if (!empty($filters['type'])) {
            $forms = array_filter($forms, function($form) use ($filters) {
                return $form['type'] === $filters['type'];
            });
        }

        return array_values($forms);
    }
}