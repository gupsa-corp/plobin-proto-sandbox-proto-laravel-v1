<?php

namespace Tests\Feature\Pms\KanbanBoard\KanbanColumnDataAttributes;

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

    public function test_칸반_컬럼에_필수_데이터_속성이_있다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        $content = $response->getContent();

        // 모든 컬럼의 data-column-id 속성 확인
        $this->assertStringContainsString(
            'data-column-id="planning"',
            $content,
            'planning 컬럼의 data-column-id가 없습니다'
        );

        $this->assertStringContainsString(
            'data-column-id="in_progress"',
            $content,
            'in_progress 컬럼의 data-column-id가 없습니다'
        );

        $this->assertStringContainsString(
            'data-column-id="review"',
            $content,
            'review 컬럼의 data-column-id가 없습니다'
        );

        $this->assertStringContainsString(
            'data-column-id="completed"',
            $content,
            'completed 컬럼의 data-column-id가 없습니다'
        );

        // kanban-column 클래스 확인
        $this->assertStringContainsString(
            'class="kanban-column',
            $content,
            'kanban-column 클래스가 없습니다'
        );
    }
}
