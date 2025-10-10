<?php

namespace Tests\Feature\Pms\CalendarView\CreateRequest\Success\CancelButton;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_모달에서_취소_버튼_클릭_시_폼이_초기화된다(): void
    {
        // Given: 폼에 데이터 입력
        $component = Livewire::test(CalendarLivewire::class)
            ->set('eventForm.title', '테스트 제목')
            ->set('eventForm.description', '테스트 설명입니다만')
            ->set('showCreateModal', true);

        // When: 취소 버튼 클릭
        $component->call('closeCreateModal');

        // Then: 모달 닫히고 폼 초기화
        $component->assertSet('showCreateModal', false)
            ->assertSet('eventForm.title', '')
            ->assertSet('eventForm.description', '')
            ->assertSet('eventForm.date', '')
            ->assertSet('eventForm.estimated_hours', 1)
            ->assertSet('eventForm.priority', 'medium');
    }
}
