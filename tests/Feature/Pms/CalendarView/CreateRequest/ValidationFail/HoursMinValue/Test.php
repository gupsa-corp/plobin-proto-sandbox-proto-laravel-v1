<?php

namespace Tests\Feature\Pms\CalendarView\CreateRequest\ValidationFail\HoursMinValue;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_예상_소요시간이_1_미만이면_유효성_검사_실패(): void
    {
        // When: 소요시간 0 입력
        Livewire::test(CalendarLivewire::class)
            ->set('eventForm.title', '제목입니다')
            ->set('eventForm.description', '설명은 10자 이상입니다.')
            ->set('eventForm.date', now()->addDays(1)->format('Y-m-d'))
            ->set('eventForm.estimated_hours', 0)
            ->call('createEvent')
            ->assertHasErrors(['eventForm.estimated_hours' => 'min']);
    }
}
