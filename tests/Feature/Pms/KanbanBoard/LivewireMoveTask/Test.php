<?php

namespace Tests\Feature\Pms\KanbanBoard\LivewireMoveTask;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\KanbanBoard\Livewire as KanbanBoardLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlobinPmsProjectSeeder::class);
    }

    public function test_moveTask_메서드가_호출된다(): void
    {
        $component = Livewire::test(KanbanBoardLivewire::class);

        // moveTask 메서드 호출이 에러 없이 실행되는지 확인
        $component->call('moveTask', 1, 'in_progress', 'completed')
            ->assertOk();

        // projects 프로퍼티가 존재하는지 확인
        $this->assertNotEmpty($component->get('projects'));
    }

    public function test_moveTask_메서드가_에러_없이_실행된다_2(): void
    {
        $component = Livewire::test(KanbanBoardLivewire::class);

        $component->call('moveTask', 1, 'in_progress', 'completed')
            ->assertOk();

        // 컴포넌트가 정상 작동하는지 확인
        $this->assertNotEmpty($component->get('projects'));
        $this->assertNotEmpty($component->get('columns'));
    }

    public function test_컬럼_데이터가_정상적으로_로드된다(): void
    {
        $component = Livewire::test(KanbanBoardLivewire::class);

        $columns = $component->get('columns');
        $this->assertCount(4, $columns);

        $columnIds = array_column($columns, 'id');
        $this->assertContains('planning', $columnIds);
        $this->assertContains('in_progress', $columnIds);
        $this->assertContains('review', $columnIds);
        $this->assertContains('completed', $columnIds);
    }

    public function test_프로젝트_데이터가_정상적으로_로드된다(): void
    {
        $component = Livewire::test(KanbanBoardLivewire::class);

        $projects = $component->get('projects');
        $this->assertNotEmpty($projects);
        $this->assertGreaterThanOrEqual(6, count($projects));
    }
}
