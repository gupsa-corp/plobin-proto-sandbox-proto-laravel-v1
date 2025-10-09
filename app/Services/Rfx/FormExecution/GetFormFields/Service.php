<?php

namespace App\Services\Rfx\FormExecution\GetFormFields;

class Service
{
    public function execute($formId): array
    {
        $formFields = [
            1 => [ // 계약서 데이터 추출
                ['name' => 'contract_type', 'label' => '계약 유형', 'type' => 'select', 'options' => ['용역', '매매', '임대차'], 'required' => true, 'defaultValue' => ''],
                ['name' => 'extraction_fields', 'label' => '추출할 필드', 'type' => 'checkbox', 'options' => ['계약자명', '계약금액', '계약기간', '조건'], 'required' => true, 'defaultValue' => []],
                ['name' => 'output_format', 'label' => '출력 형식', 'type' => 'select', 'options' => ['Excel', 'JSON', 'CSV'], 'required' => true, 'defaultValue' => 'Excel'],
                ['name' => 'include_confidence', 'label' => '신뢰도 포함', 'type' => 'checkbox', 'options' => ['예'], 'required' => false, 'defaultValue' => []]
            ],
            2 => [ // 송장 데이터 검증
                ['name' => 'validation_rules', 'label' => '검증 규칙', 'type' => 'checkbox', 'options' => ['금액 일치성', '날짜 유효성', '세금 계산'], 'required' => true, 'defaultValue' => []],
                ['name' => 'threshold', 'label' => '오차 허용 범위', 'type' => 'number', 'required' => true, 'defaultValue' => '0.01'],
                ['name' => 'report_errors_only', 'label' => '오류만 보고', 'type' => 'checkbox', 'options' => ['예'], 'required' => false, 'defaultValue' => []]
            ],
            3 => [ // 회의록 요약 생성
                ['name' => 'summary_length', 'label' => '요약 길이', 'type' => 'select', 'options' => ['간단', '보통', '상세'], 'required' => true, 'defaultValue' => '보통'],
                ['name' => 'extract_action_items', 'label' => '액션 아이템 추출', 'type' => 'checkbox', 'options' => ['예'], 'required' => false, 'defaultValue' => ['예']],
                ['name' => 'include_attendees', 'label' => '참석자 포함', 'type' => 'checkbox', 'options' => ['예'], 'required' => false, 'defaultValue' => []]
            ],
            4 => [ // 재무제표 분석
                ['name' => 'analysis_type', 'label' => '분석 유형', 'type' => 'checkbox', 'options' => ['수익성', '안정성', '성장성', '효율성'], 'required' => true, 'defaultValue' => []],
                ['name' => 'comparison_period', 'label' => '비교 기간', 'type' => 'select', 'options' => ['전년 대비', '전분기 대비', '업계 평균'], 'required' => true, 'defaultValue' => '전년 대비'],
                ['name' => 'chart_inclusion', 'label' => '차트 포함', 'type' => 'checkbox', 'options' => ['예'], 'required' => false, 'defaultValue' => []]
            ],
            5 => [ // 품질 보고서 검토
                ['name' => 'compliance_standards', 'label' => '준수 기준', 'type' => 'checkbox', 'options' => ['ISO 9001', 'ISO 14001', '내부 기준'], 'required' => true, 'defaultValue' => []],
                ['name' => 'severity_level', 'label' => '심각도 수준', 'type' => 'select', 'options' => ['낮음', '보통', '높음'], 'required' => true, 'defaultValue' => '보통']
            ]
        ];

        return $formFields[$formId] ?? [];
    }
}