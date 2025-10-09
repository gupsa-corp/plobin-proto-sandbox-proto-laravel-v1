<?php

namespace App\Services\Pms\Kanban;

/**
 * PMS 도메인 칸반 보드 서비스
 */
class Service
{
    public function execute(): array
    {
        return [
            'columns' => [
                [
                    'id' => 'planning',
                    'title' => '계획중',
                    'color' => 'bg-yellow-50',
                    'borderColor' => 'border-yellow-200',
                    'order' => 1
                ],
                [
                    'id' => 'in_progress',
                    'title' => '진행중',
                    'color' => 'bg-blue-50',
                    'borderColor' => 'border-blue-200',
                    'order' => 2
                ],
                [
                    'id' => 'review',
                    'title' => '검토중',
                    'color' => 'bg-purple-50',
                    'borderColor' => 'border-purple-200',
                    'order' => 3
                ],
                [
                    'id' => 'completed',
                    'title' => '완료',
                    'color' => 'bg-green-50',
                    'borderColor' => 'border-green-200',
                    'order' => 4
                ]
            ],
            'projects' => [
                [
                    'id' => 1,
                    'title' => '웹사이트 리뉴얼',
                    'description' => '기존 웹사이트의 완전한 리뉴얼을 진행합니다.',
                    'status' => 'in_progress',
                    'assignee' => '김개발',
                    'priority' => 'high',
                    'dueDate' => '2024-12-15',
                    'tags' => ['웹개발', 'UI/UX'],
                    'progress' => 75
                ],
                [
                    'id' => 2,
                    'title' => '모바일 앱 개발',
                    'description' => '고객용 모바일 애플리케이션 개발',
                    'status' => 'planning',
                    'assignee' => '이모바일',
                    'priority' => 'medium',
                    'dueDate' => '2024-12-30',
                    'tags' => ['모바일', 'React Native'],
                    'progress' => 25
                ],
                [
                    'id' => 3,
                    'title' => 'API 서버 구축',
                    'description' => '마이크로서비스 아키텍처 기반 API 서버 구축',
                    'status' => 'completed',
                    'assignee' => '박백엔드',
                    'priority' => 'high',
                    'dueDate' => '2024-09-30',
                    'tags' => ['백엔드', 'API'],
                    'progress' => 100
                ],
                [
                    'id' => 4,
                    'title' => '데이터베이스 최적화',
                    'description' => '기존 데이터베이스 성능 최적화 작업',
                    'status' => 'review',
                    'assignee' => '정데이터',
                    'priority' => 'medium',
                    'dueDate' => '2024-11-30',
                    'tags' => ['데이터베이스', '최적화'],
                    'progress' => 90
                ],
                [
                    'id' => 5,
                    'title' => '사용자 인증 시스템',
                    'description' => 'OAuth 2.0 기반 인증 시스템 구현',
                    'status' => 'planning',
                    'assignee' => '최보안',
                    'priority' => 'high',
                    'dueDate' => '2024-11-15',
                    'tags' => ['보안', 'OAuth'],
                    'progress' => 10
                ],
                [
                    'id' => 6,
                    'title' => 'CI/CD 파이프라인',
                    'description' => '자동화된 배포 시스템 구축',
                    'status' => 'in_progress',
                    'assignee' => '한데브옵스',
                    'priority' => 'medium',
                    'dueDate' => '2024-10-25',
                    'tags' => ['DevOps', 'CI/CD'],
                    'progress' => 60
                ]
            ]
        ];
    }
}