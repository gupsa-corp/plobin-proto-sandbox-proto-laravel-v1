<?php

namespace Tests\Feature\Pms\CalendarView\Navigation\SwitchToMonthView;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_뷰_모드를_월별로_전환할_수_있다(): void
    {
        // Given: 주별 뷰 모드
        $component = Livewire::test(CalendarLivewire::class)
            ->set('viewMode', 'week');

        // When: 월별 버튼 클릭
        $component->call('changeViewMode', 'month');

        // Then: 월별 뷰로 전환
        $component->assertSet('viewMode', 'month');
    }
}
