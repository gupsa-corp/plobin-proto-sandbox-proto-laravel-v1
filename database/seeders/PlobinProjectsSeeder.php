<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plobin\Project;

class PlobinProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'name' => '웹사이트 리뉴얼 프로젝트',
                'description' => '기존 웹사이트의 완전한 리뉴얼을 진행합니다.',
                'status' => 'in_progress',
                'priority' => 'high',
                'progress' => 75,
                'start_date' => '2024-09-01',
                'end_date' => '2024-12-15',
                'team' => ['김개발', '이디자인', '박기획'],
            ],
            [
                'name' => '모바일 앱 개발',
                'description' => '고객용 모바일 애플리케이션 개발',
                'status' => 'planning',
                'priority' => 'medium',
                'progress' => 25,
                'start_date' => '2024-10-01',
                'end_date' => '2024-12-30',
                'team' => ['최개발', '임디자인'],
            ],
            [
                'name' => 'API 서버 구축',
                'description' => '마이크로서비스 아키텍처 기반 API 서버 구축',
                'status' => 'completed',
                'priority' => 'high',
                'progress' => 100,
                'start_date' => '2024-08-01',
                'end_date' => '2024-09-30',
                'team' => ['정백엔드', '조데브옵스'],
            ],
            [
                'name' => '데이터베이스 최적화',
                'description' => '기존 데이터베이스 성능 최적화 작업',
                'status' => 'pending',
                'priority' => 'low',
                'progress' => 0,
                'start_date' => '2024-11-01',
                'end_date' => '2024-11-30',
                'team' => ['한디비에이'],
            ]
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
