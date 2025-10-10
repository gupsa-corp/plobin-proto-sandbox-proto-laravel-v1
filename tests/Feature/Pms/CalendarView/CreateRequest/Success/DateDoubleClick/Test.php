<?php

namespace Tests\Feature\Pms\CalendarView\CreateRequest\Success\DateDoubleClick;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_날짜_더블클릭_시_해당_날짜로_모달이_열린다(): void
    {
        // Given: 특정 날짜
        $targetDate = now()->addDays(3)->format('Y-m-d');

        // When: openCreateModal 호출
        Livewire::test(CalendarLivewire::class)
            ->call('openCreateModal', $targetDate)
            ->assertSet('showCreateModal', true)
            ->assertSet('eventForm.date', $targetDate);
    }
}
