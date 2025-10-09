<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SandboxRecordLink extends Model
{
    protected $table = 'sandbox_pms_record_links';

    protected $fillable = [
        'field_link_id',
        'source_record_id',
        'target_record_id'
    ];

    public $timestamps = false; // created_at만 있음
    
    protected $dates = ['created_at'];

    public function fieldLink(): BelongsTo
    {
        return $this->belongsTo(SandboxFieldLink::class, 'field_link_id');
    }

    public function sourceRecord(): BelongsTo
    {
        return $this->belongsTo(SandboxRecord::class, 'source_record_id');
    }

    public function targetRecord(): BelongsTo
    {
        return $this->belongsTo(SandboxRecord::class, 'target_record_id');
    }
}