<?php

namespace Tests\Feature\Pms\CalendarView\Navigation\SelectDate;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_날짜_클릭_시_선택_날짜가_업데이트된다(): void
    {
        // Given: 특정 날짜
        $targetDate = '2025-01-20';

        // When: 날짜 클릭
        $component = Livewire::test(CalendarLivewire::class)
            ->call('selectDate', $targetDate);

        // Then: selectedDate 업데이트
        $component->assertSet('selectedDate', $targetDate);
    }
}
