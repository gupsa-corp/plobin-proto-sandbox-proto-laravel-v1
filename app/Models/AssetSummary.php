<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetSummary extends Model
{
    use HasFactory;

    protected $table = 'sandbox_asset_summaries';

    protected $fillable = [
        'asset_id',
        'ai_summary',
        'helpful_content',
        'analysis_status',
        'analysis_metadata',
        'version_count',
        'current_version',
    ];

    protected $casts = [
        'analysis_metadata' => 'array',
        'version_count' => 'integer',
        'current_version' => 'integer',
    ];

    protected $attributes = [
        'analysis_status' => 'completed',
        'version_count' => 1,
        'current_version' => 1,
    ];

    public function documentAsset()
    {
        return $this->belongsTo(DocumentAsset::class, 'asset_id');
    }

    public function summaryVersions()
    {
        return $this->hasMany(SummaryVersion::class, 'summary_id');
    }

    public function getCurrentVersionContent()
    {
        return $this->summaryVersions()
            ->where('version_number', $this->current_version)
            ->first();
    }

    public function getVersionHistory()
    {
        return $this->summaryVersions()
            ->orderBy('version_number', 'desc')
            ->get();
    }

    public function incrementVersion()
    {
        $this->increment('version_count');
        $this->increment('current_version');
        return $this;
    }

    public function setCurrentVersion($version)
    {
        $this->update(['current_version' => $version]);
        return $this;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'processing' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800'
        ];

        return $colors[$this->analysis_status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusIconAttribute()
    {
        $icons = [
            'processing' => '⏳',
            'completed' => '✅',
            'failed' => '❌'
        ];

        return $icons[$this->analysis_status] ?? '❓';
    }

    public function scopeByAssetId($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('analysis_status', $status);
    }
}