<?php

namespace Tests\Feature\Pms\CalendarView\DisplayRequests\DateGrouping;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;
use Carbon\Carbon;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_날짜별_요청이_그룹핑되어_표시된다(): void
    {
        // Given: 같은 날짜에 여러 요청 생성
        $userId = DB::table('plobin_users')->insertGetId([
            'name' => '테스트 사용자',
            'email' => 'test@example.com',
            'role' => 'analyst',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $targetDate = now()->addDays(5)->format('Y-m-d');

        DB::table('plobin_analysis_requests')->insert([
            [
                'title' => '요청 1',
                'description' => '첫 번째 요청입니다.',
                'status' => 'pending',
                'priority' => 'high',
                'requester_id' => $userId,
                'required_by' => $targetDate,
                'estimated_hours' => 3,
                'completed_percentage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '요청 2',
                'description' => '두 번째 요청입니다.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'requester_id' => $userId,
                'required_by' => $targetDate,
                'estimated_hours' => 5,
                'completed_percentage' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // When: 캘린더 데이터 로드
        $component = Livewire::test(CalendarLivewire::class)
            ->call('loadCalendarData');

        // Then: 같은 날짜에 2개 요청이 그룹핑
        $calendarEvents = $component->get('calendarEvents');
        $this->assertArrayHasKey($targetDate, $calendarEvents);
        $this->assertCount(2, $calendarEvents[$targetDate]);
    }
}