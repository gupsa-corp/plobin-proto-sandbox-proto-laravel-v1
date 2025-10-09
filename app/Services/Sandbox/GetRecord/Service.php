<?php

namespace App\Services\Sandbox\GetRecord;

use App\Models\Plobin\SandboxRecord;

/**
 * 에어테이블 스타일 레코드 상세 조회 서비스
 */
class Service
{
    public function execute(int $recordId): array
    {
        $record = SandboxRecord::with([
            'table.fields',
            'fieldValues.field'
        ])->find($recordId);

        if (!$record) {
            return ['success' => false, 'message' => 'Record not found'];
        }

        $recordData = [
            'id' => $record->id,
            'record_number' => $record->record_number,
            'table' => [
                'id' => $record->table->id,
                'name' => $record->table->name
            ],
            'fields' => []
        ];

        foreach ($record->table->fields as $field) {
            $fieldValue = $record->fieldValues->where('field_id', $field->id)->first();
            $value = $fieldValue ? $fieldValue->getValue() : null;

            $recordData['fields'][$field->slug] = [
                'field_id' => $field->id,
                'field_name' => $field->name,
                'field_type' => $field->field_type,
                'value' => $value,
                'display_value' => $this->formatDisplayValue($value, $field)
            ];
        }

        return [
            'success' => true,
            'data' => $recordData
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