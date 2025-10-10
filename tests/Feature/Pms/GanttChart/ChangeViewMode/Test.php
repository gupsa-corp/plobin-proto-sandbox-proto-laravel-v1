<?php

namespace Tests\Feature\Pms\GanttChart\ChangeViewMode;

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

    public function test_뷰_모드를_변경할_수_있다(): void
    {
        $component = Livewire::test(GanttChartLivewire::class);

        // 초기 viewMode 확인
        $this->assertEquals('Month', $component->get('viewMode'));

        // viewMode 변경
        $component->call('changeViewMode', 'Week');

        // 변경된 viewMode 확인
        $this->assertEquals('Week', $component->get('viewMode'), 'viewMode가 변경되지 않았습니다');

        // 데이터가 다시 로드되는지 확인
        $this->assertNotEmpty($component->get('projects'), '데이터가 다시 로드되지 않았습니다');
    }
}
