<?php

namespace Tests\Feature\Pms\CalendarView\PageLoad\BasicLoad;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_캘린더_페이지가_정상적으로_로드된다(): void
    {
        // Given: 테스트 사용자 생성
        $userId = DB::table('plobin_users')->insertGetId([
            'name' => '테스트 사용자',
            'email' => 'test@example.com',
            'role' => 'analyst',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // When: 캘린더 페이지 접속
        $response = $this->get('/pms/calendar');

        // Then: 페이지가 정상 렌더링
        $response->assertStatus(200);
        $response->assertSee('캘린더 뷰');
        $response->assertSee('프로젝트 일정을 캘린더로 확인하세요');
        $response->assertSee('일정 추가');
        $response->assertSee('필터');
        $response->assertSee('주별');
        $response->assertSee('월별');
        $response->assertSee('오늘');
    }
}
