<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SandboxTable extends Model
{
    protected $table = 'sandbox_pms_tables';

    protected $fillable = [
        'base_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'primary_field_id',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function base(): BelongsTo
    {
        return $this->belongsTo(SandboxBase::class, 'base_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(SandboxField::class, 'table_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function allFields(): HasMany
    {
        return $this->hasMany(SandboxField::class, 'table_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(SandboxRecord::class, 'table_id')
            ->where('is_active', true);
    }

    public function views(): HasMany
    {
        return $this->hasMany(SandboxView::class, 'table_id');
    }

    public function primaryField(): BelongsTo
    {
        return $this->belongsTo(SandboxField::class, 'primary_field_id');
    }
}