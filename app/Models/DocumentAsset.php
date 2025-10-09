<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAsset extends Model
{
    use HasFactory;

    protected $table = 'sandbox_document_assets';

    protected $fillable = [
        'file_id',
        'asset_type',
        'section_title',
        'order_index',
        'content',
        'metadata',
        'status',
    ];

    protected $casts = [
        'metadata' => 'array',
        'order_index' => 'integer',
    ];

    protected $attributes = [
        'status' => 'pending',
        'order_index' => 0,
    ];

    public const ASSET_TYPES = [
        'introduction' => '서론/개요',
        'methodology' => '방법론',
        'findings' => '주요 발견사항',
        'analysis' => '분석 결과',
        'conclusion' => '결론',
        'recommendation' => '권고사항',
        'technical_spec' => '기술 사양',
        'data_analysis' => '데이터 분석',
        'case_study' => '사례 연구',
        'appendix' => '부록',
        'reference' => '참고문헌',
        'summary' => '요약',
        'other' => '기타'
    ];

    public function uploadedFile()
    {
        return $this->belongsTo(UploadedFile::class, 'file_id');
    }

    public function assetSummary()
    {
        return $this->hasOne(AssetSummary::class, 'asset_id');
    }

    public function getAssetTypeNameAttribute()
    {
        return self::ASSET_TYPES[$this->asset_type] ?? '알 수 없음';
    }

    public function getAssetTypeColorAttribute()
    {
        $colors = [
            'introduction' => 'bg-blue-100 text-blue-800',
            'methodology' => 'bg-green-100 text-green-800',
            'findings' => 'bg-purple-100 text-purple-800',
            'analysis' => 'bg-yellow-100 text-yellow-800',
            'conclusion' => 'bg-red-100 text-red-800',
            'recommendation' => 'bg-indigo-100 text-indigo-800',
            'technical_spec' => 'bg-gray-100 text-gray-800',
            'data_analysis' => 'bg-pink-100 text-pink-800',
            'case_study' => 'bg-teal-100 text-teal-800',
            'appendix' => 'bg-orange-100 text-orange-800',
            'reference' => 'bg-cyan-100 text-cyan-800',
            'summary' => 'bg-emerald-100 text-emerald-800',
            'other' => 'bg-slate-100 text-slate-800'
        ];

        return $colors[$this->asset_type] ?? 'bg-gray-100 text-gray-800';
    }

    public function getAssetTypeIconAttribute()
    {
        $icons = [
            'introduction' => '📖',
            'methodology' => '⚙️',
            'findings' => '🔍',
            'analysis' => '📊',
            'conclusion' => '🎯',
            'recommendation' => '💡',
            'technical_spec' => '🛠️',
            'data_analysis' => '📈',
            'case_study' => '📋',
            'appendix' => '📎',
            'reference' => '📚',
            'summary' => '📝',
            'other' => '📄'
        ];

        return $icons[$this->asset_type] ?? '📄';
    }

    public function getContentPreviewAttribute()
    {
        return mb_substr(strip_tags($this->content), 0, 200) . (mb_strlen($this->content) > 200 ? '...' : '');
    }

    public function scopeByFileId($query, $fileId)
    {
        return $query->where('file_id', $fileId);
    }

    public function scopeByAssetType($query, $assetType)
    {
        return $query->where('asset_type', $assetType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    public function scopeWithSummary($query)
    {
        return $query->with('assetSummary');
    }
}