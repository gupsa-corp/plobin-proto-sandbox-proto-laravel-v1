<?php

namespace Tests\Feature\Pms\KanbanBoard\PageLoad;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_칸반_보드_페이지가_정상적으로_로드된다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);
        $response->assertSee('칸반 보드');
        $response->assertSee('드래그하여 프로젝트 상태를 변경하세요');
    }
}
