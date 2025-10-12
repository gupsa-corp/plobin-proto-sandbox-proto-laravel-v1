<?php

namespace App\Services\Pms\UserPermissions;

/**
 * PMS 도메인 사용자 권한 관리 서비스
 */
class Service
{
    public function execute(): array
    {
        return [
            'currentUser' => [
                'name' => '김관리자',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'department' => 'IT팀',
                'joinDate' => '2024-01-01'
            ],
            'permissions' => [
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
            ],
            'roles' => [
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
            ]
        ];
    }
}
