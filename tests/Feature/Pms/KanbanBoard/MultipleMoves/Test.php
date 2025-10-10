<?php

namespace Tests\Feature\Pms\KanbanBoard\MultipleMoves;

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

    public function test_프로젝트를_여러_컬럼을_거쳐_이동할_수_있다(): void
    {
        // planning 상태인 프로젝트 생성
        $project = Project::create([
            'title' => '테스트 프로젝트',
            'status' => 'planning',
            'progress' => 0,
        ]);

        $component = Livewire::test(KanbanBoardLivewire::class);

        // 1단계: planning -> in_progress
        $component->call('moveTask', $project->id, 'planning', 'in_progress');
        $project->refresh();
        $this->assertEquals('in_progress', $project->status);
        $this->assertEquals(25, $project->progress);

        // 2단계: in_progress -> review
        $component->call('moveTask', $project->id, 'in_progress', 'review');
        $project->refresh();
        $this->assertEquals('review', $project->status);
        $this->assertEquals(80, $project->progress);

        // 3단계: review -> completed
        $component->call('moveTask', $project->id, 'review', 'completed');
        $project->refresh();
        $this->assertEquals('completed', $project->status);
        $this->assertEquals(100, $project->progress);

        // 4단계: completed -> review (되돌리기)
        $component->call('moveTask', $project->id, 'completed', 'review');
        $project->refresh();
        $this->assertEquals('review', $project->status);
        $this->assertGreaterThanOrEqual(80, $project->progress);
    }
}
