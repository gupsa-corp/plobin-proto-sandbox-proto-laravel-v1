<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalysisRequest extends Model
{
    protected $table = 'plobin_analysis_requests';
    
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'requester_id',
        'assignee_id',
        'required_by',
        'estimated_hours',
        'completed_percentage',
        'completed_at',
        'cancelled_at',
        'cancel_reason'
    ];

    protected $casts = [
        'required_by' => 'date',
        'estimated_hours' => 'integer',
        'completed_percentage' => 'integer',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(PlobinUser::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(PlobinUser::class, 'assignee_id');
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(
            UploadedFile::class,
            'plobin_analysis_request_files',
            'analysis_request_id',
            'uploaded_file_id'
        );
    }

    public function documentAnalyses(): HasMany
    {
        return $this->hasMany(DocumentAnalysis::class, 'request_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assignee_id', $userId);
    }

    public function scopeRequestedBy($query, $userId)
    {
        return $query->where('requester_id', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('required_by', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function getIsOverdueAttribute()
    {
        return $this->required_by && 
               $this->required_by->isPast() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'pending' => 'yellow',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}
