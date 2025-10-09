<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    
    protected $table = 'plobin_projects';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\ProjectFactory::new();
    }

    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'progress',
        'start_date',
        'end_date',
        'team'
    ];

    protected $casts = [
        'team' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'progress' => 'integer'
    ];
}