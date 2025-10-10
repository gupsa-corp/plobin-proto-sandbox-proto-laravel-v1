<?php

namespace Tests\Feature\Pms\KanbanBoard\MoveTaskCompletedToReview;

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

    public function test_완료된_프로젝트를_review로_되돌리면_진행률이_유지된다(): void
    {
        // completed 상태인 프로젝트 생성 (진행률 100%)
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'completed',
            'progress' => 100,
        ]);

        $component = Livewire::test(KanbanBoardLivewire::class);

        // moveTask 호출 (되돌리기)
        $component->call('moveTask', $project->id, 'completed', 'review');

        // 데이터베이스에서 프로젝트 다시 조회
        $project->refresh();

        // 상태 변경 확인
        $this->assertEquals('review', $project->status, '상태가 review로 변경되지 않았습니다');

        // 진행률이 80% 이상인지 확인 (review로 이동 시 최소 80%)
        $this->assertGreaterThanOrEqual(80, $project->progress, '진행률이 80% 이상이어야 합니다');
    }
}
