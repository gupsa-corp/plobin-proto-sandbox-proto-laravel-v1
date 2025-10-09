<?php

namespace App\Models\Plobin;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'plobin_projects';

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