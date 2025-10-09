<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SandboxView extends Model
{
    protected $table = 'sandbox_pms_views';

    protected $fillable = [
        'table_id',
        'name',
        'view_type',
        'filter_config',
        'sort_config',
        'field_config',
        'group_config',
        'is_default',
        'created_by'
    ];

    protected $casts = [
        'filter_config' => 'array',
        'sort_config' => 'array',
        'field_config' => 'array',
        'group_config' => 'array',
        'is_default' => 'boolean'
    ];

    public const VIEW_TYPES = [
        'grid' => '그리드',
        'kanban' => '칸반',
        'calendar' => '캘린더',
        'gallery' => '갤러리',
        'form' => '폼'
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(SandboxTable::class, 'table_id');
    }

    public function getViewTypeNameAttribute(): string
    {
        return self::VIEW_TYPES[$this->view_type] ?? $this->view_type;
    }
}