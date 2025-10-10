<?php

namespace Tests\Feature\Pms\KanbanBoard\DataExtractionInOnEnd;

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

    public function test_onEnd_콜백에서_드래그_데이터를_추출한다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        $content = $response->getContent();

        // evt.item.dataset.taskId 추출 확인
        $this->assertStringContainsString(
            'evt.item.dataset.taskId',
            $content,
            'taskId 추출 코드가 없습니다'
        );

        // evt.from.dataset.columnId 추출 확인
        $this->assertStringContainsString(
            'evt.from.dataset.columnId',
            $content,
            'fromColumn 추출 코드가 없습니다'
        );

        // evt.to.dataset.columnId 추출 확인
        $this->assertStringContainsString(
            'evt.to.dataset.columnId',
            $content,
            'toColumn 추출 코드가 없습니다'
        );

        // 변수 할당 확인
        $this->assertStringContainsString(
            'const taskId',
            $content,
            'taskId 변수가 선언되지 않았습니다'
        );

        $this->assertStringContainsString(
            'const fromColumn',
            $content,
            'fromColumn 변수가 선언되지 않았습니다'
        );

        $this->assertStringContainsString(
            'const toColumn',
            $content,
            'toColumn 변수가 선언되지 않았습니다'
        );
    }
}
