<?php

namespace Tests\Feature\Pms\CalendarView\DisplayRequests\ExcludeCancelled;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_취소된_요청은_표시되지_않는다(): void
    {
        // Given: 취소된 요청 생성
        $userId = DB::table('plobin_users')->insertGetId([
            'name' => '테스트 사용자',
            'email' => 'test@example.com',
            'role' => 'analyst',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $targetDate = now()->addDays(3)->format('Y-m-d');

        DB::table('plobin_analysis_requests')->insert([
            [
                'title' => '취소된 요청',
                'description' => '취소된 요청입니다.',
                'status' => 'cancelled',
                'priority' => 'medium',
                'requester_id' => $userId,
                'required_by' => $targetDate,
                'estimated_hours' => 5,
                'completed_percentage' => 0,
                'cancelled_at' => now(),
                'cancel_reason' => '테스트 취소',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // When: 캘린더 데이터 로드
        $component = Livewire::test(CalendarLivewire::class)
            ->call('loadCalendarData');

        // Then: 취소된 요청은 표시되지 않음
        $requests = $component->get('requests');
        $this->assertEmpty($requests);
    }
}