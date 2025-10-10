<?php

namespace Tests\Feature\Pms\CalendarView\ChangeViewModeToMonth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarViewLivewire;
use App\Models\User;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_월별_뷰_모드로_전환된다(): void
    {
        // Given: 사용자가 로그인되어 있고
        $user = User::factory()->create();
        $this->actingAs($user);

        // When: 캘린더 컴포넌트를 마운트하고 월별 뷰로 전환하면
        Livewire::test(CalendarViewLivewire::class)
            ->call('changeViewMode', 'month')
            // Then: viewMode가 'month'로 설정된다
            ->assertSet('viewMode', 'month');
    }
}
