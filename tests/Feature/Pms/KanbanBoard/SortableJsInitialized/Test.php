<?php

namespace Tests\Feature\Pms\KanbanBoard\SortableJsInitialized;

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

    public function test_SortableJS_초기화_스크립트가_존재한다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        $content = $response->getContent();

        // livewire:init 이벤트 리스너 확인
        $this->assertStringContainsString(
            "document.addEventListener('livewire:init'",
            $content,
            'livewire:init 이벤트 리스너가 없습니다'
        );

        // new Sortable 호출 확인
        $this->assertStringContainsString(
            'new Sortable(column',
            $content,
            'new Sortable 초기화 코드가 없습니다'
        );

        // 초기화 완료 로그 확인
        $this->assertStringContainsString(
            'SortableJS initialized',
            $content,
            'SortableJS 초기화 완료 로그가 없습니다'
        );

        // querySelectorAll('.kanban-column') 확인
        $this->assertStringContainsString(
            "querySelectorAll('.kanban-column')",
            $content,
            'kanban-column 선택자가 없습니다'
        );
    }
}
