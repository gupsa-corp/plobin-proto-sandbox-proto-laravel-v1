<?php

namespace Tests\Feature\Pms\KanbanBoard\SameColumnMoveSkip;

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

    public function test_같은_컬럼_내_이동은_스킵한다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        $content = $response->getContent();

        // 같은 컬럼 체크 로직 확인 (줄바꿈을 고려한 정규식)
        $pattern = '/if\s*\(\s*fromColumn\s*===\s*toColumn\s*\)\s*\{?\s*return/s';
        $this->assertMatchesRegularExpression(
            $pattern,
            $content,
            '같은 컬럼 이동 스킵 로직이 없습니다'
        );

        // fromColumn과 toColumn 비교 확인
        $this->assertStringContainsString('fromColumn === toColumn', $content);
        $this->assertStringContainsString('return', $content);
    }
}
