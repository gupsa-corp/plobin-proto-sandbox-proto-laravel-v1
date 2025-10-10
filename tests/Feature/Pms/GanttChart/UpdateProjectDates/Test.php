<?php

namespace Tests\Feature\Pms\GanttChart\UpdateProjectDates;

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

    public function test_프로젝트_날짜를_업데이트할_수_있다(): void
    {
        $component = Livewire::test(GanttChartLivewire::class);

        $projects = $component->get('projects');
        $firstProjectId = $projects[0]['id'];

        // 날짜 업데이트
        $newStartDate = '2025-01-01';
        $newEndDate = '2025-03-31';

        $component->call('updateProjectDates', $firstProjectId, $newStartDate, $newEndDate);

        // 업데이트된 프로젝트 확인
        $updatedProjects = $component->get('projects');
        $updatedProject = collect($updatedProjects)->firstWhere('id', $firstProjectId);

        $this->assertEquals($newStartDate, $updatedProject['startDate'], '시작 날짜가 업데이트되지 않았습니다');
        $this->assertEquals($newEndDate, $updatedProject['endDate'], '종료 날짜가 업데이트되지 않았습니다');

        // 이벤트 디스패치 확인
        $component->assertDispatched('project-updated');
    }
}
