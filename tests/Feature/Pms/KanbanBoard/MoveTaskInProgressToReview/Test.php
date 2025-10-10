<?php

namespace Tests\Feature\Pms\KanbanBoard\MoveTaskInProgressToReview;

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

    public function test_프로젝트를_in_progress에서_review로_이동하면_진행률이_80퍼센트가_된다(): void
    {
        // in_progress 상태인 프로젝트 생성 (진행률 50%)
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'in_progress',
            'progress' => 50,
        ]);

        $component = Livewire::test(KanbanBoardLivewire::class);

        // moveTask 호출
        $component->call('moveTask', $project->id, 'in_progress', 'review');

        // 데이터베이스에서 프로젝트 다시 조회
        $project->refresh();

        // 상태 변경 확인
        $this->assertEquals('review', $project->status, '상태가 review로 변경되지 않았습니다');

        // 진행률 변경 확인 (50% -> 80%)
        $this->assertEquals(80, $project->progress, '진행률이 80%로 변경되지 않았습니다');
    }
}
