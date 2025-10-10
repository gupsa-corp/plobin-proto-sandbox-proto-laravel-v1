<?php

namespace Tests\Feature\Pms\CalendarView\DisplayRequests\UserNames;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_요청자와_담당자_이름이_조회된다(): void
    {
        // Given: 요청자와 담당자가 있는 요청 생성
        $requesterId = DB::table('plobin_users')->insertGetId([
            'name' => '요청자A',
            'email' => 'requester@example.com',
            'role' => 'analyst',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $assigneeId = DB::table('plobin_users')->insertGetId([
            'name' => '담당자B',
            'email' => 'assignee@example.com',
            'role' => 'reviewer',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $targetDate = now()->addDays(2)->format('Y-m-d');

        DB::table('plobin_analysis_requests')->insert([
            'title' => '할당된 요청',
            'description' => '담당자가 있는 요청입니다.',
            'status' => 'in_progress',
            'priority' => 'high',
            'requester_id' => $requesterId,
            'assignee_id' => $assigneeId,
            'required_by' => $targetDate,
            'estimated_hours' => 8,
            'completed_percentage' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // When: 캘린더 데이터 로드
        $component = Livewire::test(CalendarLivewire::class)
            ->call('loadCalendarData');

        // Then: 요청자와 담당자 이름 확인
        $requests = $component->get('requests');
        $this->assertNotEmpty($requests);
        $this->assertEquals('요청자A', $requests[0]['requester']);
        $this->assertEquals('담당자B', $requests[0]['assignee']);
    }
}