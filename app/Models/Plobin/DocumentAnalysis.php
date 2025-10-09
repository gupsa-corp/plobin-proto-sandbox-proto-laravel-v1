<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAnalysis extends Model
{
    protected $table = 'plobin_document_analyses';
    
    protected $fillable = [
        'file_id',
        'request_id',
        'status',
        'summary',
        'keywords',
        'categories',
        'confidence_score',
        'extracted_data',
        'recommendations',
        'document_type',
        'keyword_count',
        'page_count',
        'error_message',
        'analyzed_at'
    ];

    protected $casts = [
        'keywords' => 'array',
        'categories' => 'array',
        'confidence_score' => 'decimal:2',
        'extracted_data' => 'array',
        'recommendations' => 'array',
        'keyword_count' => 'integer',
        'page_count' => 'integer',
        'analyzed_at' => 'datetime'
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'file_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(AnalysisRequest::class, 'request_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeHighConfidence($query, $threshold = 80.0)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }

    public function scopeByDocumentType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function getFormattedConfidenceAttribute()
    {
        return $this->confidence_score ? number_format($this->confidence_score, 1) . '%' : null;
    }

    public function getConfidenceLevelAttribute()
    {
        if (!$this->confidence_score) return 'unknown';
        
        return match(true) {
            $this->confidence_score >= 90 => 'excellent',
            $this->confidence_score >= 80 => 'good',
            $this->confidence_score >= 70 => 'fair',
            default => 'poor'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'green',
            'analyzing' => 'blue',
            'pending' => 'yellow',
            'error' => 'red',
            default => 'gray'
        };
    }
}
