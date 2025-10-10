<?php

namespace Tests\Feature\Pms\CalendarView\CreateRequest\ValidationFail\DateRequired;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_날짜가_없으면_유효성_검사_실패(): void
    {
        // When: 날짜 없이 제출
        Livewire::test(CalendarLivewire::class)
            ->set('eventForm.title', '제목입니다')
            ->set('eventForm.description', '설명은 10자 이상입니다.')
            ->set('eventForm.date', '')
            ->set('eventForm.estimated_hours', 5)
            ->call('createEvent')
            ->assertHasErrors(['eventForm.date' => 'required']);
    }
}
