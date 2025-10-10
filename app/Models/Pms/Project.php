<?php

namespace App\Models\Pms;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'plobin_pms_projects';

    protected $fillable = [
        'title',
        'description',
        'status',
        'assignee',
        'priority',
        'due_date',
        'tags',
        'progress',
    ];

    protected $casts = [
        'tags' => 'array',
        'due_date' => 'date',
        'progress' => 'integer',
    ];
}
