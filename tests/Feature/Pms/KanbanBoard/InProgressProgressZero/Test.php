<?php

namespace Tests\Feature\Pms\KanbanBoard\InProgressProgressZero;

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

    public function test_진행률이_0인_프로젝트를_in_progress로_이동하면_25퍼센트가_된다(): void
    {
        // planning 상태이며 진행률이 0%인 프로젝트 생성
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'planning',
            'progress' => 0,
        ]);

        $component = Livewire::test(KanbanBoardLivewire::class);

        // moveTask 호출
        $component->call('moveTask', $project->id, 'planning', 'in_progress');

        // 데이터베이스에서 프로젝트 다시 조회
        $project->refresh();

        // 진행률이 25%로 변경되었는지 확인
        $this->assertEquals(25, $project->progress, '진행률이 25%로 변경되어야 합니다');
    }
}
