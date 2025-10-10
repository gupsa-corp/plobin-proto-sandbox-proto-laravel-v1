<?php

namespace App\Services\Pms\GetTypeByStatus;

class Service
{
    public function execute(string $status): string
    {
        return match($status) {
            'pending' => 'task',
            'in_progress' => 'meeting',
            'completed' => 'event',
            default => 'task',
        };
    }
}
