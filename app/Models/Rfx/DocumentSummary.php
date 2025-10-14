<?php

namespace App\Models\Rfx;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class DocumentSummary extends Model
{
    use HasUlids;

    protected $table = 'plobin_document_summaries';

    protected $fillable = [
        'document_id',
        'total_pages',
        'total_blocks',
        'json_version',
        'document_version',
    ];

    public function pageSummaries()
    {
        return $this->hasMany(PageSummary::class, 'document_summary_id');
    }
}
