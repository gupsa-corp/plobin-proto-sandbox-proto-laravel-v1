<?php

namespace Tests\Feature\Pms\KanbanBoard\SortableJsCdnLoaded;

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

    public function test_SortableJS_CDN이_로드된다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        $content = $response->getContent();

        // SortableJS CDN 스크립트 태그 확인
        $this->assertStringContainsString(
            'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js',
            $content,
            'SortableJS CDN 스크립트가 로드되지 않았습니다'
        );

        // script 태그 형식 확인
        $this->assertStringContainsString(
            '<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>',
            $content,
            'SortableJS CDN 스크립트 태그 형식이 올바르지 않습니다'
        );
    }
}
