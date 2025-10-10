<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlobinPmsProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => '웹사이트 리뉴얼',
                'description' => '기존 웹사이트의 완전한 리뉴얼을 진행합니다.',
                'status' => 'in_progress',
                'assignee' => '김개발',
                'priority' => 'high',
                'due_date' => '2024-12-15',
                'tags' => json_encode(['웹개발', 'UI/UX']),
                'progress' => 75
            ],
            [
                'title' => '모바일 앱 개발',
                'description' => '고객용 모바일 애플리케이션 개발',
                'status' => 'planning',
                'assignee' => '이모바일',
                'priority' => 'medium',
                'due_date' => '2024-12-30',
                'tags' => json_encode(['모바일', 'React Native']),
                'progress' => 25
            ],
            [
                'title' => 'API 서버 구축',
                'description' => '마이크로서비스 아키텍처 기반 API 서버 구축',
                'status' => 'completed',
                'assignee' => '박백엔드',
                'priority' => 'high',
                'due_date' => '2024-09-30',
                'tags' => json_encode(['백엔드', 'API']),
                'progress' => 100
            ],
            [
                'title' => '데이터베이스 최적화',
                'description' => '기존 데이터베이스 성능 최적화 작업',
                'status' => 'review',
                'assignee' => '정데이터',
                'priority' => 'medium',
                'due_date' => '2024-11-30',
                'tags' => json_encode(['데이터베이스', '최적화']),
                'progress' => 90
            ],
            [
                'title' => '사용자 인증 시스템',
                'description' => 'OAuth 2.0 기반 인증 시스템 구현',
                'status' => 'planning',
                'assignee' => '최보안',
                'priority' => 'high',
                'due_date' => '2024-11-15',
                'tags' => json_encode(['보안', 'OAuth']),
                'progress' => 10
            ],
            [
                'title' => 'CI/CD 파이프라인',
                'description' => '자동화된 배포 시스템 구축',
                'status' => 'in_progress',
                'assignee' => '한데브옵스',
                'priority' => 'medium',
                'due_date' => '2024-10-25',
                'tags' => json_encode(['DevOps', 'CI/CD']),
                'progress' => 60
            ]
        ];

        foreach ($projects as $project) {
            \App\Models\Pms\Project::create($project);
        }
    }
}
