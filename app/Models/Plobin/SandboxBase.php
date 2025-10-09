<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SandboxBase extends Model
{
    protected $table = 'sandbox_pms_bases';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function tables(): HasMany
    {
        return $this->hasMany(SandboxTable::class, 'base_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function allTables(): HasMany
    {
        return $this->hasMany(SandboxTable::class, 'base_id');
    }
}