<?php

namespace Tests\Feature\Pms\KanbanBoard\ReviewProgressLessThan80;

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

    public function test_진행률이_80퍼센트_미만인_프로젝트를_review로_이동하면_80퍼센트가_된다(): void
    {
        // in_progress 상태이며 진행률이 30%인 프로젝트 생성
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'in_progress',
            'progress' => 30,
        ]);

        $component = Livewire::test(KanbanBoardLivewire::class);

        // moveTask 호출
        $component->call('moveTask', $project->id, 'in_progress', 'review');

        // 데이터베이스에서 프로젝트 다시 조회
        $project->refresh();

        // 진행률이 80%로 변경되었는지 확인
        $this->assertEquals(80, $project->progress, '진행률이 80%로 변경되어야 합니다');
    }
}
