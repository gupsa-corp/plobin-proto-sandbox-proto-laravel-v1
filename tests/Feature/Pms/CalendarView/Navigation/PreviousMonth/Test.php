<?php

namespace Tests\Feature\Pms\CalendarView\Navigation\PreviousMonth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;
use Carbon\Carbon;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_이전_월로_이동할_수_있다(): void
    {
        // Given: 현재 날짜
        $currentDate = Carbon::parse('2025-01-15');
        $component = Livewire::test(CalendarLivewire::class)
            ->set('currentDate', $currentDate->format('Y-m-d'))
            ->set('viewMode', 'month');

        // When: 이전 버튼 클릭
        $component->call('previousPeriod');

        // Then: 이전 달로 이동
        $expectedDate = $currentDate->copy()->subMonth()->format('Y-m-d');
        $component->assertSet('currentDate', $expectedDate);
    }
}
