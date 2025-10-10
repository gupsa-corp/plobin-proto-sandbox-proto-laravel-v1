<?php

namespace Tests\Feature\Pms\GanttChart\SelectProject;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\GanttChart\Livewire as GanttChartLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlobinPmsKanbanSeeder::class);
    }

    public function test_프로젝트를_선택할_수_있다(): void
    {
        $component = Livewire::test(GanttChartLivewire::class);

        // 초기 selectedProject 확인
        $this->assertNull($component->get('selectedProject'));

        // 프로젝트 선택
        $component->call('selectProject', 1);

        // 선택된 프로젝트 확인
        $this->assertEquals(1, $component->get('selectedProject'), '프로젝트가 선택되지 않았습니다');
    }
}
