<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plobin\Project;

class PlobinProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::create([
            'name' => '웹사이트 리뉴얼 프로젝트',
            'description' => '회사 웹사이트를 새롭게 리뉴얼하는 프로젝트입니다.',
            'status' => 'in_progress',
            'priority' => 'high',
            'progress' => 75,
            'start_date' => '2024-01-01',
            'end_date' => '2024-06-30',
            'team' => ['김개발', '이디자인', '박기획']
        ]);

        Project::create([
            'name' => '모바일 앱 개발',
            'description' => '새로운 모바일 애플리케이션을 개발하는 프로젝트입니다.',
            'status' => 'planning',
            'priority' => 'medium',
            'progress' => 25,
            'start_date' => '2024-03-01',
            'end_date' => '2024-12-31',
            'team' => ['최모바일', '정백엔드']
        ]);

        Project::create([
            'name' => 'API 서버 구축',
            'description' => 'RESTful API 서버를 구축하는 프로젝트입니다.',
            'status' => 'completed',
            'priority' => 'low',
            'progress' => 100,
            'start_date' => '2023-09-01',
            'end_date' => '2023-12-31',
            'team' => ['한서버', '노데이터베이스']
        ]);
    }
}