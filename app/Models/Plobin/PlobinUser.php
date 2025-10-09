<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlobinUser extends Model
{
    protected $table = 'plobin_users';
    
    protected $fillable = [
        'name',
        'email',
        'role',
        'department',
        'is_active',
        'last_login_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime'
    ];

    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(UploadedFile::class, 'uploaded_by');
    }

    public function requestedAnalyses(): HasMany
    {
        return $this->hasMany(AnalysisRequest::class, 'requester_id');
    }

    public function assignedAnalyses(): HasMany
    {
        return $this->hasMany(AnalysisRequest::class, 'assignee_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
