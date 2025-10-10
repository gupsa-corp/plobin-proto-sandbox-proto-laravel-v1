<?php

namespace Tests\Feature\Pms\KanbanBoard\ColumnsDisplay;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\KanbanBoard\Livewire as KanbanBoardLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_4개의_칸반_컬럼이_표시된다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);
        $response->assertSee('계획중');
        $response->assertSee('진행중');
        $response->assertSee('검토중');
        $response->assertSee('완료');
    }
}
