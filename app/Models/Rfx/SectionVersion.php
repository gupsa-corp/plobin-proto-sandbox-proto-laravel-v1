<?php

namespace App\Models\Rfx;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class SectionVersion extends Model
{
    use HasUlids;

    protected $table = 'plobin_section_versions';

    public $timestamps = false; // created_at만 사용

    protected $fillable = [
        'section_analysis_id',
        'version_number',
        'version_display_name',
        'ai_summary',
        'is_current',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function sectionAnalysis()
    {
        return $this->belongsTo(SectionAnalysis::class, 'section_analysis_id');
    }
}
