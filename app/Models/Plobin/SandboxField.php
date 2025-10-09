<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SandboxField extends Model
{
    protected $table = 'sandbox_pms_fields';

    protected $fillable = [
        'table_id',
        'name',
        'slug',
        'field_type',
        'field_config',
        'is_required',
        'is_primary',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'field_config' => 'array',
        'is_required' => 'boolean',
        'is_primary' => 'boolean',
        'is_active' => 'boolean'
    ];

    // 에어테이블 스타일 필드 타입 정의
    public const FIELD_TYPES = [
        'singleLineText' => '한줄 텍스트',
        'multilineText' => '여러줄 텍스트',
        'richText' => '서식 있는 텍스트',
        'number' => '숫자',
        'currency' => '통화',
        'percent' => '퍼센트',
        'date' => '날짜',
        'dateTime' => '날짜/시간',
        'duration' => '기간',
        'singleSelect' => '단일 선택',
        'multipleSelect' => '다중 선택',
        'checkbox' => '체크박스',
        'rating' => '평점',
        'linkedRecord' => '연결된 레코드',
        'lookup' => '조회',
        'rollup' => '롤업',
        'count' => '개수',
        'formula' => '수식',
        'attachment' => '첨부파일',
        'url' => 'URL',
        'email' => '이메일',
        'phoneNumber' => '전화번호',
        'autoNumber' => '자동 번호',
        'createdTime' => '생성 시간',
        'lastModifiedTime' => '마지막 수정 시간',
        'createdBy' => '생성자',
        'lastModifiedBy' => '마지막 수정자'
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(SandboxTable::class, 'table_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(SandboxFieldValue::class, 'field_id');
    }

    public function link(): HasOne
    {
        return $this->hasOne(SandboxFieldLink::class, 'source_field_id');
    }

    public function getFieldTypeNameAttribute(): string
    {
        return self::FIELD_TYPES[$this->field_type] ?? $this->field_type;
    }
}