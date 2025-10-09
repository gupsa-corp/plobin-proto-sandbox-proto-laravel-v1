<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SandboxFieldValue extends Model
{
    protected $table = 'sandbox_pms_field_values';

    protected $fillable = [
        'record_id',
        'field_id',
        'value_text',
        'value_number',
        'value_date',
        'value_boolean',
        'value_json'
    ];

    protected $casts = [
        'value_number' => 'decimal:8',
        'value_date' => 'datetime',
        'value_boolean' => 'boolean',
        'value_json' => 'array'
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(SandboxRecord::class, 'record_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(SandboxField::class, 'field_id');
    }

    /**
     * 필드 타입에 따라 적절한 값 반환
     */
    public function getValue()
    {
        if ($this->value_text !== null) return $this->value_text;
        if ($this->value_number !== null) return $this->value_number;
        if ($this->value_date !== null) return $this->value_date;
        if ($this->value_boolean !== null) return $this->value_boolean;
        if ($this->value_json !== null) return $this->value_json;

        return null;
    }

    /**
     * 필드 타입에 따라 값 설정
     */
    public function setValue($value, string $fieldType): void
    {
        // 기본값 초기화
        $this->value_text = null;
        $this->value_number = null;
        $this->value_date = null;
        $this->value_boolean = null;
        $this->value_json = null;

        switch ($fieldType) {
            case 'singleLineText':
            case 'multilineText':
            case 'richText':
            case 'email':
            case 'phoneNumber':
            case 'url':
                $this->value_text = $value;
                break;

            case 'number':
            case 'currency':
            case 'percent':
            case 'rating':
                $this->value_number = $value;
                break;

            case 'date':
            case 'dateTime':
            case 'createdTime':
            case 'lastModifiedTime':
                $this->value_date = $value;
                break;

            case 'checkbox':
                $this->value_boolean = $value;
                break;

            case 'singleSelect':
            case 'multipleSelect':
            case 'linkedRecord':
            case 'attachment':
            case 'lookup':
            case 'rollup':
            case 'formula':
                $this->value_json = $value;
                break;

            default:
                $this->value_text = $value;
        }
    }
}