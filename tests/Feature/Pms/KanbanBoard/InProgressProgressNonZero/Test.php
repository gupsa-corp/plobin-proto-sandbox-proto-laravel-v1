<?php

namespace Tests\Feature\Pms\KanbanBoard\InProgressProgressNonZero;

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

    public function test_진행률이_0이_아닌_프로젝트를_in_progress로_이동하면_진행률이_유지된다(): void
    {
        // review 상태이며 진행률이 85%인 프로젝트 생성
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'review',
            'progress' => 85,
        ]);

        $component = Livewire::test(KanbanBoardLivewire::class);

        // moveTask 호출 (되돌리기)
        $component->call('moveTask', $project->id, 'review', 'in_progress');

        // 데이터베이스에서 프로젝트 다시 조회
        $project->refresh();

        // 진행률이 유지되는지 확인
        $this->assertEquals(85, $project->progress, '진행률이 유지되어야 합니다');
    }
}
