<?php

namespace Tests\Feature\Pms\KanbanBoard\DragAndDrop;

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

    public function test_드래그_가능한_카드_속성이_렌더링된다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        // draggable 속성 확인
        $content = $response->getContent();
        $this->assertStringContainsString('draggable="true"', $content);

        // SortableJS 드래그 앤 드롭 클래스 확인
        $this->assertStringContainsString('class="kanban-card', $content);
        $this->assertStringContainsString('class="kanban-column', $content);

        // data 속성 확인
        $this->assertStringContainsString('data-task-id', $content);
        $this->assertStringContainsString('data-column-id', $content);

        // SortableJS 초기화 확인
        $this->assertStringContainsString('livewire:init', $content);
        $this->assertStringContainsString('new Sortable', $content);
        $this->assertStringContainsString('SortableJS initialized', $content);
    }
}
