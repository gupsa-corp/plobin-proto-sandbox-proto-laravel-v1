<?php

namespace Tests\Feature\Pms\KanbanBoard\MoveTaskReviewToCompleted;

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

    public function test_프로젝트를_review에서_completed로_이동하면_진행률이_100퍼센트가_된다(): void
    {
        // review 상태인 프로젝트 생성 (진행률 80%)
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'review',
            'progress' => 80,
        ]);

        $component = Livewire::test(KanbanBoardLivewire::class);

        // moveTask 호출
        $component->call('moveTask', $project->id, 'review', 'completed');

        // 데이터베이스에서 프로젝트 다시 조회
        $project->refresh();

        // 상태 변경 확인
        $this->assertEquals('completed', $project->status, '상태가 completed로 변경되지 않았습니다');

        // 진행률 변경 확인 (80% -> 100%)
        $this->assertEquals(100, $project->progress, '진행률이 100%로 변경되지 않았습니다');
    }
}
