<?php

namespace Tests\Feature\Pms\GanttChart\LivewireComponent;

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

    public function test_Livewire_컴포넌트가_정상적으로_마운트된다(): void
    {
        $component = Livewire::test(GanttChartLivewire::class);

        $component->assertOk();

        // 초기 프로퍼티 확인
        $this->assertNotNull($component->get('projects'), 'projects 프로퍼티가 null입니다');
        $this->assertEquals('3months', $component->get('timeRange'), 'timeRange 초기값이 잘못되었습니다');
        $this->assertEquals('Month', $component->get('viewMode'), 'viewMode 초기값이 잘못되었습니다');
        $this->assertNull($component->get('selectedProject'), 'selectedProject 초기값이 null이어야 합니다');
    }
}
