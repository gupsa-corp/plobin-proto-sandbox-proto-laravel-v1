<?php

namespace Tests\Feature\Pms\GanttChart\PageHeader;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlobinPmsKanbanSeeder::class);
    }

    public function test_페이지_헤더와_설명이_표시된다(): void
    {
        $response = $this->get('/pms/gantt');

        $response->assertStatus(200);

        $content = $response->getContent();

        // 제목 확인
        $this->assertStringContainsString(
            '<h1',
            $content,
            'h1 제목 태그가 없습니다'
        );

        $this->assertStringContainsString(
            '간트 차트',
            $content,
            '간트 차트 제목이 없습니다'
        );

        // 설명 확인
        $this->assertStringContainsString(
            '프로젝트 일정',
            $content,
            '프로젝트 일정 설명이 없습니다'
        );
    }
}
