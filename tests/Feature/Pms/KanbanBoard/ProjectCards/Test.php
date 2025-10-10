<?php

namespace Tests\Feature\Pms\KanbanBoard\ProjectCards;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlobinPmsProjectSeeder::class);
    }

    public function test_프로젝트_카드가_표시된다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);
        $response->assertSee('웹사이트 리뉴얼');
        $response->assertSee('모바일 앱 개발');
        $response->assertSee('API 서버 구축');
        $response->assertSee('데이터베이스 최적화');
        $response->assertSee('사용자 인증 시스템');
        $response->assertSee('CI/CD 파이프라인');
    }
}
