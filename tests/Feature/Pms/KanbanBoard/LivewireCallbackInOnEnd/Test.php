<?php

namespace Tests\Feature\Pms\KanbanBoard\LivewireCallbackInOnEnd;

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

    public function test_onEnd_콜백에서_Livewire_메서드를_호출한다(): void
    {
        $response = $this->get('/pms/kanban');

        $response->assertStatus(200);

        $content = $response->getContent();

        // onEnd 콜백 내부에서 Livewire 컴포넌트 메서드 호출 확인 (컴파일된 형태)
        // @this.call()은 Livewire.find().call()로 컴파일됨
        $hasLivewireCall =
            str_contains($content, 'Livewire.find(') ||
            str_contains($content, '.call(') ||
            str_contains($content, "call('moveTask");

        $this->assertTrue(
            $hasLivewireCall,
            'onEnd 콜백에서 Livewire 메서드 호출이 없습니다'
        );

        // moveTask 메서드 호출 확인
        $this->assertStringContainsString(
            'moveTask',
            $content,
            'moveTask 메서드 호출이 없습니다'
        );

        // taskId, fromColumn, toColumn 변수 사용 확인
        $this->assertStringContainsString('taskId', $content);
        $this->assertStringContainsString('fromColumn', $content);
        $this->assertStringContainsString('toColumn', $content);
    }
}
