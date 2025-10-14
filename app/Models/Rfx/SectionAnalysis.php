<?php

namespace App\Models\Rfx;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class SectionAnalysis extends Model
{
    use HasUlids;

    protected $table = 'plobin_section_analyses';

    protected $fillable = [
        'page_summary_id',
        'block_id',
        'block_index',
        'section_title',
        'asset_type',
        'asset_type_name',
        'asset_type_icon',
        'original_content',
        'ai_summary',
        'helpful_content',
        'current_version_number',
    ];

    public function pageSummary()
    {
        return $this->belongsTo(PageSummary::class, 'page_summary_id');
    }

    public function versions()
    {
        return $this->hasMany(SectionVersion::class, 'section_analysis_id');
    }

    public function currentVersion()
    {
        return $this->hasOne(SectionVersion::class, 'section_analysis_id')
                    ->where('is_current', true);
    }
}
