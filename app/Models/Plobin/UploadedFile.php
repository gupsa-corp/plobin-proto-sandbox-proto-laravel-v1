<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UploadedFile extends Model
{
    protected $table = 'plobin_uploaded_files';
    
    protected $fillable = [
        'uuid',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'status',
        'uploaded_by',
        'tags',
        'description',
        'download_count',
        'analyzed_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'analyzed_at' => 'datetime'
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(PlobinUser::class, 'uploaded_by');
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(DocumentAnalysis::class, 'file_id');
    }

    public function analysisRequests(): BelongsToMany
    {
        return $this->belongsToMany(
            AnalysisRequest::class,
            'plobin_analysis_request_files',
            'uploaded_file_id',
            'analysis_request_id'
        );
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAnalyzed($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }
}
