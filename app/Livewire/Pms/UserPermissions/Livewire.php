<?php

namespace App\Livewire\Pms\UserPermissions;

use Livewire\Component;

class Livewire extends Component
{
    public $currentUser;
    public $permissions;
    public $roles;

    public function mount()
    {
        $this->loadUserPermissions();
    }

    public function loadUserPermissions()
    {
        // 현재 사용자 정보 (실제로는 Auth::user()를 사용)
        $this->currentUser = [
            'name' => '김관리자',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'department' => 'IT팀',
            'joinDate' => '2024-01-01'
        ];

        // 사용자 권한 목록
        $this->permissions = [
            'dashboard' => [
                'name' => '대시보드',
                'read' => true,
                'write' => true,
                'delete' => false
            ],
            'projects' => [
                'name' => '프로젝트 관리',
                'read' => true,
                'write' => true,
                'delete' => true
            ],
            'users' => [
                'name' => '사용자 관리',
                'read' => true,
                'write' => false,
                'delete' => false
            ],
            'reports' => [
                'name' => '보고서',
                'read' => true,
                'write' => true,
                'delete' => false
            ]
        ];

        // 역할 목록
        $this->roles = [
            [
                'name' => 'admin',
                'displayName' => '관리자',
                'description' => '모든 권한을 가진 관리자 역할'
            ],
            [
                'name' => 'manager',
                'displayName' => '매니저',
                'description' => '프로젝트 관리 권한을 가진 매니저 역할'
            ],
            [
                'name' => 'user',
                'displayName' => '사용자',
                'description' => '기본 사용자 역할'
            ]
        ];
    }

    public function render()
    {
        return view('700-page-pms-user-permissions.000-index')
            ->layout('700-page-pms-common.000-layout', ['title' => '권한관리']);
    }
}