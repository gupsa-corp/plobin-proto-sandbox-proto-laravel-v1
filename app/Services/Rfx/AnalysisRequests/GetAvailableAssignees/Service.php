<?php

namespace App\Services\Rfx\AnalysisRequests\GetAvailableAssignees;

use App\Models\Plobin\PlobinUser;

class Service
{
    public function execute(): array
    {
        $assignees = PlobinUser::active()
            ->whereIn('role', ['analyst', 'reviewer', 'manager'])
            ->get(['id', 'name', 'role', 'department']);

        return $assignees->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $this->getRoleDisplayName($user->role),
                'department' => $user->department
            ];
        })->toArray();
    }

    private function getRoleDisplayName($role): string
    {
        return match($role) {
            'analyst' => '분석가',
            'reviewer' => '검토자',
            'manager' => '관리자',
            'admin' => '시스템 관리자',
            default => $role
        };
    }
}