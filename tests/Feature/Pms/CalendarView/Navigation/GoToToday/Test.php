<?php

namespace Tests\Feature\Pms\CalendarView\Navigation\GoToToday;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;
use Carbon\Carbon;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_오늘_버튼으로_현재_날짜로_이동할_수_있다(): void
    {
        // Given: 과거 날짜로 이동한 상태
        $pastDate = Carbon::parse('2024-01-01');
        $component = Livewire::test(CalendarLivewire::class)
            ->set('currentDate', $pastDate->format('Y-m-d'));

        // When: 오늘 버튼 클릭
        $component->call('goToToday');

        // Then: 오늘 날짜로 이동
        $component->assertSet('currentDate', now()->format('Y-m-d'));
    }
}
