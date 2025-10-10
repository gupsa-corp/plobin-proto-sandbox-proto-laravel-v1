<?php

namespace App\Services\Pms\GetColorByPriority;

class Service
{
    public function execute(string $priority): string
    {
        return match($priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'blue',
            'low' => 'gray',
            default => 'blue',
        };
    }
}
