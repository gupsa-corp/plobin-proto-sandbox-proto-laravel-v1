<?php

namespace Tests\Feature\Pms\CalendarView\PageLoad\LegendDisplay;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_캘린더_범례가_표시된다(): void
    {
        // When: 캘린더 페이지 접속
        $response = $this->get('/pms/calendar');

        // Then: 범례 및 사용법 섹션 표시
        $response->assertStatus(200);
        $response->assertSee('범례 및 사용법');
        $response->assertSee('우선순위');
        $response->assertSee('사용법');
    }
}
