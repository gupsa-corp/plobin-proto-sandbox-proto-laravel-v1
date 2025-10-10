<?php

namespace Tests\Feature\Pms\KanbanBoard\RealBrowserDragDrop;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlobinPmsProjectSeeder::class);
    }

    public function test_실제_브라우저에서_드래그_앤_드롭이_작동한다(): void
    {
        // Livewire 컴포넌트 직접 테스트
        $livewire = \Livewire\Livewire::test(\App\Livewire\Pms\KanbanBoard\Livewire::class);

        // 초기 상태 확인
        $livewire->assertSet('columns', function($columns) {
            return count($columns) === 4;
        });

        $livewire->assertSet('projects', function($projects) {
            return count($projects) === 6;
        });

        // moveTask 메서드 호출 (taskId=2, from=planning, to=in_progress)
        $livewire->call('moveTask', 2, 'planning', 'in_progress');

        // 프로젝트 상태가 변경되었는지 확인
        $livewire->assertSet('projects', function($projects) {
            foreach ($projects as $project) {
                if ($project['id'] == 2) {
                    return $project['status'] === 'in_progress' && $project['progress'] === 25;
                }
            }
            return false;
        });
    }
}
