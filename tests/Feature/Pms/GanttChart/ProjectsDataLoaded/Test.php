<?php

namespace Tests\Feature\Pms\GanttChart\ProjectsDataLoaded;

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

    public function test_프로젝트_데이터가_정상적으로_로드된다(): void
    {
        $component = Livewire::test(GanttChartLivewire::class);

        $projects = $component->get('projects');

        // 프로젝트 데이터 확인
        $this->assertIsArray($projects, 'projects가 배열이 아닙니다');
        $this->assertNotEmpty($projects, 'projects가 비어있습니다');

        // 첫 번째 프로젝트 구조 확인
        $firstProject = $projects[0];
        $this->assertArrayHasKey('id', $firstProject, 'id 키가 없습니다');
        $this->assertArrayHasKey('name', $firstProject, 'name 키가 없습니다');
        $this->assertArrayHasKey('startDate', $firstProject, 'startDate 키가 없습니다');
        $this->assertArrayHasKey('endDate', $firstProject, 'endDate 키가 없습니다');
        $this->assertArrayHasKey('progress', $firstProject, 'progress 키가 없습니다');
    }
}
