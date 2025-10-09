<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryVersion extends Model
{
    use HasFactory;

    protected $table = 'sandbox_summary_versions';

    public $timestamps = false;
    
    protected $fillable = [
        'summary_id',
        'version_number',
        'ai_summary',
        'helpful_content',
        'edit_type',
        'edit_notes',
        'is_current',
        'created_at',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'is_current' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected $attributes = [
        'edit_type' => 'user_edit',
        'is_current' => false,
    ];

    public const EDIT_TYPES = [
        'ai_generated' => 'AI 생성',
        'user_edit' => '사용자 편집',
        'auto_improved' => '자동 개선'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = now();
            
            if ($model->is_current) {
                static::where('summary_id', $model->summary_id)
                    ->update(['is_current' => false]);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('is_current') && $model->is_current) {
                static::where('summary_id', $model->summary_id)
                    ->where('id', '!=', $model->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public function assetSummary()
    {
        return $this->belongsTo(AssetSummary::class, 'summary_id');
    }

    public function getEditTypeNameAttribute()
    {
        return self::EDIT_TYPES[$this->edit_type] ?? '알 수 없음';
    }

    public function getEditTypeColorAttribute()
    {
        $colors = [
            'ai_generated' => 'bg-blue-100 text-blue-800',
            'user_edit' => 'bg-green-100 text-green-800',
            'auto_improved' => 'bg-purple-100 text-purple-800'
        ];

        return $colors[$this->edit_type] ?? 'bg-gray-100 text-gray-800';
    }

    public function getEditTypeIconAttribute()
    {
        $icons = [
            'ai_generated' => '🤖',
            'user_edit' => '👤',
            'auto_improved' => '⚡'
        ];

        return $icons[$this->edit_type] ?? '📝';
    }

    public function setAsCurrent()
    {
        static::where('summary_id', $this->summary_id)
            ->update(['is_current' => false]);
            
        $this->update(['is_current' => true]);
        return $this;
    }

    public function scopeBySummaryId($query, $summaryId)
    {
        return $query->where('summary_id', $summaryId);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeByEditType($query, $editType)
    {
        return $query->where('edit_type', $editType);
    }

    public function scopeOrderedByVersion($query, $direction = 'desc')
    {
        return $query->orderBy('version_number', $direction);
    }

    public static function getNextVersionNumber($summaryId)
    {
        return static::where('summary_id', $summaryId)
            ->max('version_number') + 1;
    }

    public static function getCurrentVersion($summaryId)
    {
        return static::where('summary_id', $summaryId)
            ->where('is_current', true)
            ->first();
    }
}