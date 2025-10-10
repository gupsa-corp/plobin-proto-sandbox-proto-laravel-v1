<?php

namespace Tests\Feature\Pms\KanbanBoard\SortableJsConfiguration;

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

    public function test_SortableJS_설정이_올바르게_구성된다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        $content = $response->getContent();

        // group 설정 확인
        $this->assertStringContainsString(
            "group: 'kanban'",
            $content,
            'SortableJS group 설정이 없습니다'
        );

        // animation 설정 확인
        $this->assertStringContainsString(
            'animation: 150',
            $content,
            'SortableJS animation 설정이 없습니다'
        );

        // ghostClass 설정 확인
        $this->assertStringContainsString(
            "ghostClass: 'opacity-50'",
            $content,
            'SortableJS ghostClass 설정이 없습니다'
        );

        // onEnd 콜백 확인
        $this->assertStringContainsString(
            'onEnd: function(evt)',
            $content,
            'SortableJS onEnd 콜백이 없습니다'
        );
    }
}
