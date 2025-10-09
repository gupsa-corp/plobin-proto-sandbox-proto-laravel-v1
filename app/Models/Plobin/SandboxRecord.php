<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SandboxRecord extends Model
{
    protected $table = 'sandbox_pms_records';

    protected $fillable = [
        'table_id',
        'record_number',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function sandboxTable(): BelongsTo
    {
        return $this->belongsTo(SandboxTable::class, 'table_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(SandboxTable::class, 'table_id');
    }

    public function fieldValues(): HasMany
    {
        return $this->hasMany(SandboxFieldValue::class, 'record_id');
    }

    public function sourceLinks(): HasMany
    {
        return $this->hasMany(SandboxRecordLink::class, 'source_record_id');
    }

    public function targetLinks(): HasMany
    {
        return $this->hasMany(SandboxRecordLink::class, 'target_record_id');
    }

    /**
     * 특정 필드의 값을 가져오기
     */
    public function getFieldValue(int $fieldId)
    {
        $fieldValue = $this->fieldValues()->where('field_id', $fieldId)->first();
        
        if (!$fieldValue) {
            return null;
        }

        // 필드 타입에 따라 적절한 값 반환
        if ($fieldValue->value_text !== null) return $fieldValue->value_text;
        if ($fieldValue->value_number !== null) return $fieldValue->value_number;
        if ($fieldValue->value_date !== null) return $fieldValue->value_date;
        if ($fieldValue->value_boolean !== null) return $fieldValue->value_boolean;
        if ($fieldValue->value_json !== null) return $fieldValue->value_json;

        return null;
    }

    /**
     * 필드 슬러그로 값 가져오기
     */
    public function getFieldValueBySlug(string $fieldSlug)
    {
        // 관계를 안전하게 로드
        $this->loadMissing('sandboxTable.fields');
        
        $field = $this->sandboxTable->fields()->where('slug', $fieldSlug)->first();
        if (!$field) {
            return null;
        }

        return $this->getFieldValue($field->id);
    }
}