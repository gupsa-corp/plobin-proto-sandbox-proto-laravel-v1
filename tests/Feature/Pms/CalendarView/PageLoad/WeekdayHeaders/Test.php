<?php

namespace Tests\Feature\Pms\CalendarView\PageLoad\WeekdayHeaders;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_캘린더_요일_헤더가_표시된다(): void
    {
        // When: 캘린더 페이지 접속
        $response = $this->get('/pms/calendar');

        // Then: 요일 헤더 표시
        $response->assertStatus(200);
        $response->assertSee('일');
        $response->assertSee('월');
        $response->assertSee('화');
        $response->assertSee('수');
        $response->assertSee('목');
        $response->assertSee('금');
        $response->assertSee('토');
    }
}
