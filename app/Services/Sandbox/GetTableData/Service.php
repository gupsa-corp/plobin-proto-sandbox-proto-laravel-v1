<?php

namespace App\Services\Sandbox\GetTableData;

use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxRecord;

/**
 * 에어테이블 스타일 테이블 데이터 조회 서비스
 */
class Service
{
    public function execute(int $tableId, ?int $viewId = null): array
    {
        $table = SandboxTable::with(['fields' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])->find($tableId);

        if (!$table) {
            return ['success' => false, 'message' => 'Table not found'];
        }

        // 레코드 조회
        $recordsQuery = SandboxRecord::where('table_id', $tableId)
            ->where('is_active', true)
            ->with(['fieldValues.field']);

        // 뷰 설정이 있으면 적용 (향후 구현)
        if ($viewId) {
            // TODO: 뷰 필터, 정렬 적용
        }

        $records = $recordsQuery->get();

        // 에어테이블 스타일로 데이터 포맷팅
        $formattedRecords = $records->map(function($record) use ($table) {
            $recordData = [
                'id' => $record->id,
                'record_number' => $record->record_number,
                'created_at' => $record->created_at->format('Y-m-d H:i:s')
            ];

            // 각 필드별 값 매핑
            foreach ($table->fields as $field) {
                $fieldValue = $record->fieldValues->where('field_id', $field->id)->first();
                $value = $fieldValue ? $fieldValue->getValue() : null;
                
                // 필드 타입에 따른 표시 형식 조정
                $recordData['fields'][$field->slug] = [
                    'field_id' => $field->id,
                    'field_name' => $field->name,
                    'field_type' => $field->field_type,
                    'value' => $value,
                    'display_value' => $this->formatDisplayValue($value, $field)
                ];
            }

            return $recordData;
        });

        return [
            'success' => true,
            'data' => [
                'table' => [
                    'id' => $table->id,
                    'name' => $table->name,
                    'slug' => $table->slug,
                    'icon' => $table->icon,
                    'color' => $table->color
                ],
                'fields' => $table->fields->map(function($field) {
                    return [
                        'id' => $field->id,
                        'name' => $field->name,
                        'slug' => $field->slug,
                        'field_type' => $field->field_type,
                        'field_type_name' => $field->field_type_name,
                        'is_primary' => $field->is_primary
                    ];
                }),
                'records' => $formattedRecords
            ]
        ];
    }

    /**
     * 필드 타입에 따른 표시값 포맷팅
     */
    private function formatDisplayValue($value, $field): string
    {
        if ($value === null) {
            return '';
        }

        switch ($field->field_type) {
            case 'singleSelect':
            case 'multipleSelect':
                return is_array($value) ? implode(', ', $value) : (string)$value;
            
            case 'checkbox':
                return $value ? '✓' : '';
            
            case 'date':
            case 'dateTime':
                return $value instanceof \DateTime ? $value->format('Y-m-d') : $value;
            
            case 'currency':
                return is_numeric($value) ? '₩' . number_format($value) : $value;
            
            case 'percent':
                return is_numeric($value) ? (int)$value . '%' : $value;
            
            case 'rating':
                return is_numeric($value) ? str_repeat('⭐', (int)$value) : $value;
            
            default:
                return (string)$value;
        }
    }
}