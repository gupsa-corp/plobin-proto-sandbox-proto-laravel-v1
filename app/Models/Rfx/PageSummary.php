<?php

namespace App\Models\Rfx;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PageSummary extends Model
{
    use HasUlids;

    protected $table = 'plobin_page_summaries';

    protected $fillable = [
        'document_summary_id',
        'page_number',
        'block_count',
        'ai_summary',
    ];

    public function documentSummary()
    {
        return $this->belongsTo(DocumentSummary::class, 'document_summary_id');
    }

    public function sectionAnalyses()
    {
        return $this->hasMany(SectionAnalysis::class, 'page_summary_id');
    }
}
