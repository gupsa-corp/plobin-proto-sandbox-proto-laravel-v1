<?php

namespace Tests\Feature\Pms\CalendarView\Navigation\NextWeek;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;
use Carbon\Carbon;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_다음_주로_이동할_수_있다(): void
    {
        // Given: 주별 뷰, 특정 날짜
        $currentDate = Carbon::parse('2025-01-15');
        $component = Livewire::test(CalendarLivewire::class)
            ->set('currentDate', $currentDate->format('Y-m-d'))
            ->set('viewMode', 'week');

        // When: 다음 버튼 클릭
        $component->call('nextPeriod');

        // Then: 다음 주로 이동
        $expectedDate = $currentDate->copy()->addWeek()->format('Y-m-d');
        $component->assertSet('currentDate', $expectedDate);
    }
}
