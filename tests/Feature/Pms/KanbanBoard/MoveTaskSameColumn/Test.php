<?php

namespace Tests\Feature\Pms\KanbanBoard\MoveTaskSameColumn;

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

    public function test_같은_컬럼_내에서_이동해도_상태가_변경되지_않는다(): void
    {
        // planning 상태인 프로젝트 생성
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'planning',
            'progress' => 0,
        ]);

        $originalStatus = $project->status;
        $originalProgress = $project->progress;

        $component = Livewire::test(KanbanBoardLivewire::class);

        // 같은 컬럼으로 moveTask 호출
        $component->call('moveTask', $project->id, 'planning', 'planning')
            ->assertOk();

        // 상태와 진행률이 변경되지 않았는지 확인
        $project->refresh();
        $this->assertEquals($originalStatus, $project->status, '상태가 변경되어서는 안 됩니다');
        $this->assertEquals($originalProgress, $project->progress, '진행률이 변경되어서는 안 됩니다');
    }
}
