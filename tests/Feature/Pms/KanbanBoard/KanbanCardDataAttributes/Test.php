<?php

namespace Tests\Feature\Pms\KanbanBoard\KanbanCardDataAttributes;

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

    public function test_칸반_카드에_필수_데이터_속성이_있다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        $content = $response->getContent();

        // kanban-card 클래스 확인
        $this->assertStringContainsString(
            'class="kanban-card',
            $content,
            'kanban-card 클래스가 없습니다'
        );

        // draggable 속성 확인
        $this->assertStringContainsString(
            'draggable="true"',
            $content,
            'draggable 속성이 없습니다'
        );

        // data-task-id 속성 확인 (최소 1개 이상)
        $this->assertStringContainsString(
            'data-task-id=',
            $content,
            'data-task-id 속성이 없습니다'
        );

        // 실제 프로젝트 ID로 data-task-id 확인
        preg_match_all('/data-task-id="(\d+)"/', $content, $matches);
        $this->assertGreaterThanOrEqual(
            1,
            count($matches[1]),
            'data-task-id가 설정된 카드가 없습니다'
        );

        // 추출된 task-id가 유효한 숫자인지 확인
        foreach ($matches[1] as $taskId) {
            $this->assertIsNumeric($taskId, "task-id {$taskId}가 숫자가 아닙니다");
            $this->assertGreaterThan(0, (int)$taskId, "task-id {$taskId}가 0보다 커야 합니다");
        }
    }
}
