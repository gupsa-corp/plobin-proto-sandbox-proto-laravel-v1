<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SandboxFieldLink extends Model
{
    protected $table = 'sandbox_pms_field_links';

    protected $fillable = [
        'source_field_id',
        'target_table_id',
        'target_field_id',
        'link_type'
    ];

    public function sourceField(): BelongsTo
    {
        return $this->belongsTo(SandboxField::class, 'source_field_id');
    }

    public function targetTable(): BelongsTo
    {
        return $this->belongsTo(SandboxTable::class, 'target_table_id');
    }

    public function targetField(): BelongsTo
    {
        return $this->belongsTo(SandboxField::class, 'target_field_id');
    }

    public function recordLinks(): HasMany
    {
        return $this->hasMany(SandboxRecordLink::class, 'field_link_id');
    }
}