<?php

namespace Tests\Feature\Pms\CalendarView\Navigation\PreviousWeek;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;
use Carbon\Carbon;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_이전_주로_이동할_수_있다(): void
    {
        // Given: 주별 뷰, 특정 날짜
        $currentDate = Carbon::parse('2025-01-15');
        $component = Livewire::test(CalendarLivewire::class)
            ->set('currentDate', $currentDate->format('Y-m-d'))
            ->set('viewMode', 'week');

        // When: 이전 버튼 클릭
        $component->call('previousPeriod');

        // Then: 이전 주로 이동
        $expectedDate = $currentDate->copy()->subWeek()->format('Y-m-d');
        $component->assertSet('currentDate', $expectedDate);
    }
}
