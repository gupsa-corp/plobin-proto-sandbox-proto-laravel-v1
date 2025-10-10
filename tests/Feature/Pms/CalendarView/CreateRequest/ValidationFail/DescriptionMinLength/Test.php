<?php

namespace Tests\Feature\Pms\CalendarView\CreateRequest\ValidationFail\DescriptionMinLength;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_설명이_10자_미만이면_유효성_검사_실패(): void
    {
        // When: 설명 9자만 입력
        Livewire::test(CalendarLivewire::class)
            ->set('eventForm.title', '제목입니다')
            ->set('eventForm.description', '짧은설명임')
            ->set('eventForm.date', now()->addDays(1)->format('Y-m-d'))
            ->set('eventForm.estimated_hours', 5)
            ->call('createEvent')
            ->assertHasErrors(['eventForm.description' => 'min']);
    }
}
