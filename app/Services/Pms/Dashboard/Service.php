<?php

namespace App\Services\Pms\Dashboard;

/**
 * PMS 도메인 대시보드 서비스
 */
class Service
{
    public function execute(): array
    {
        return [
            'stats' => [
                'totalProjects' => 42,
                'activeProjects' => 18,
                'completedTasks' => 127,
                'pendingTasks' => 23
            ],
            'recentProjects' => [
                [
                    'id' => 1,
                    'name' => '웹사이트 리뉴얼',
                    'status' => 'in_progress',
                    'progress' => 75,
                    'dueDate' => '2024-12-15'
                ],
                [
                    'id' => 2,
                    'name' => '모바일 앱 개발',
                    'status' => 'planning',
                    'progress' => 25,
                    'dueDate' => '2024-12-30'
                ]
            ],
            'tasks' => [
                [
                    'id' => 1,
                    'title' => 'UI 디자인 검토',
                    'priority' => 'high',
                    'dueDate' => '2024-10-15'
                ],
                [
                    'id' => 2,
                    'title' => 'API 문서 작성',
                    'priority' => 'medium',
                    'dueDate' => '2024-10-20'
                ]
            ],
            'notifications' => [
                [
                    'id' => 1,
                    'message' => '새로운 프로젝트가 할당되었습니다.',
                    'time' => '10분 전'
                ],
                [
                    'id' => 2,
                    'message' => '작업 마감일이 다가왔습니다.',
                    'time' => '1시간 전'
                ]
            ]
        ];
    }
}