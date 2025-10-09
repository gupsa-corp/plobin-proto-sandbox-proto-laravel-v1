<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    use HasFactory;

    protected $table = 'sandbox_uploaded_files';

    protected $fillable = [
        'file_name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'is_analysis_requested',
        'is_analysis_completed',
        'analysis_status',
        'analysis_requested_at',
        'analysis_completed_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_analysis_requested' => 'boolean',
        'is_analysis_completed' => 'boolean',
        'analysis_requested_at' => 'datetime',
        'analysis_completed_at' => 'datetime',
    ];

    protected $attributes = [
        'file_size' => 0,
        'mime_type' => 'application/octet-stream',
        'is_analysis_requested' => false,
        'is_analysis_completed' => false,
        'analysis_status' => 'pending',
    ];

    public const ANALYSIS_STATUS = [
        'pending' => '대기중',
        'processing' => '분석중',
        'completed' => '완료',
        'failed' => '실패'
    ];

    public function documentAssets()
    {
        return $this->hasMany(DocumentAsset::class, 'file_id');
    }

    public function requestAnalysis()
    {
        $this->update([
            'is_analysis_requested' => true,
            'analysis_status' => 'processing',
            'analysis_requested_at' => now(),
        ]);

        return $this;
    }

    public function completeAnalysis($status = 'completed')
    {
        $this->update([
            'is_analysis_completed' => true,
            'analysis_status' => $status,
            'analysis_completed_at' => now(),
        ]);

        return $this;
    }

    public function failAnalysis()
    {
        $this->update([
            'analysis_status' => 'failed',
        ]);

        return $this;
    }

    public function getFileSizeFormattedAttribute()
    {
        $size = $this->file_size;
        
        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        } else {
            return $size . ' bytes';
        }
    }

    public function getAnalysisStatusNameAttribute()
    {
        return self::ANALYSIS_STATUS[$this->analysis_status] ?? '알 수 없음';
    }

    public function getAnalysisStatusColorAttribute()
    {
        $colors = [
            'pending' => 'bg-gray-100 text-gray-800',
            'processing' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800'
        ];

        return $colors[$this->analysis_status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getAnalysisStatusIconAttribute()
    {
        $icons = [
            'pending' => '⏳',
            'processing' => '🔄',
            'completed' => '✅',
            'failed' => '❌'
        ];

        return $icons[$this->analysis_status] ?? '❓';
    }

    public function scopeAnalyzed($query)
    {
        return $query->where('is_analysis_completed', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('analysis_status', $status);
    }

    public function scopeRequested($query)
    {
        return $query->where('is_analysis_requested', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}