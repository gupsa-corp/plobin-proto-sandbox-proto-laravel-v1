<?php

namespace Tests\Feature\Pms\GanttChart\PageLoad;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlobinPmsKanbanSeeder::class);
    }

    public function test_간트_차트_페이지가_정상적으로_로드된다(): void
    {
        $response = $this->get('/pms/gantt');

        $response->assertStatus(200);

        $content = $response->getContent();

        // 페이지 제목 확인
        $this->assertStringContainsString('간트 차트', $content, '간트 차트 제목이 없습니다');

        // Livewire 컴포넌트 로드 확인
        $this->assertStringContainsString('wire:snapshot', $content, 'Livewire 컴포넌트가 로드되지 않았습니다');
        $this->assertStringContainsString('pms.gantt-chart.livewire', $content, 'Gantt Livewire 컴포넌트가 없습니다');
    }
}
