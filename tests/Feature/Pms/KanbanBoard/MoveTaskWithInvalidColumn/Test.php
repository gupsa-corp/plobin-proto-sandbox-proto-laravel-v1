<?php

namespace Tests\Feature\Pms\KanbanBoard\MoveTaskWithInvalidColumn;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\KanbanBoard\Livewire as KanbanBoardLivewire;
use App\Models\Pms\Project;

class Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlobinPmsProjectSeeder::class);
    }

    public function test_유효하지_않은_컬럼으로_이동해도_에러가_발생하지_않는다(): void
    {
        // planning 상태인 프로젝트 생성
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'planning',
            'progress' => 0,
        ]);

        $component = Livewire::test(KanbanBoardLivewire::class);

        // 유효하지 않은 컬럼명으로 moveTask 호출
        $component->call('moveTask', $project->id, 'planning', 'invalid_column')
            ->assertOk();

        // 상태가 변경되었는지 확인
        $project->refresh();
        $this->assertEquals('invalid_column', $project->status, '상태가 변경되어야 합니다');
    }
}
