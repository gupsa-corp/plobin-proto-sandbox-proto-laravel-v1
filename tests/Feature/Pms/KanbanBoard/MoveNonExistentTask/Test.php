<?php

namespace Tests\Feature\Pms\KanbanBoard\MoveNonExistentTask;

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

    public function test_존재하지_않는_프로젝트를_이동해도_에러가_발생하지_않는다(): void
    {
        $component = Livewire::test(KanbanBoardLivewire::class);

        // 존재하지 않는 ID로 moveTask 호출
        $component->call('moveTask', 99999, 'planning', 'in_progress')
            ->assertOk();

        // 컴포넌트가 정상적으로 렌더링되는지 확인
        $this->assertNotEmpty($component->get('projects'));
        $this->assertNotEmpty($component->get('columns'));
    }
}
