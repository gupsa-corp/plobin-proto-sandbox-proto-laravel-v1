<?php

namespace Tests\Feature\Pms\CalendarView\DisplayRequests\PriorityColors;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_우선순위별로_올바른_색상이_매핑된다(): void
    {
        // Given: 다양한 우선순위 요청
        $userId = DB::table('plobin_users')->insertGetId([
            'name' => '테스트 사용자',
            'email' => 'test@example.com',
            'role' => 'analyst',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $baseDate = now();

        DB::table('plobin_analysis_requests')->insert([
            [
                'title' => 'Urgent 요청',
                'description' => '긴급 요청',
                'status' => 'pending',
                'priority' => 'urgent',
                'requester_id' => $userId,
                'required_by' => $baseDate->copy()->addDays(1)->format('Y-m-d'),
                'estimated_hours' => 2,
                'completed_percentage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'High 요청',
                'description' => '높은 우선순위',
                'status' => 'pending',
                'priority' => 'high',
                'requester_id' => $userId,
                'required_by' => $baseDate->copy()->addDays(2)->format('Y-m-d'),
                'estimated_hours' => 3,
                'completed_percentage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Medium 요청',
                'description' => '보통 우선순위',
                'status' => 'pending',
                'priority' => 'medium',
                'requester_id' => $userId,
                'required_by' => $baseDate->copy()->addDays(3)->format('Y-m-d'),
                'estimated_hours' => 5,
                'completed_percentage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Low 요청',
                'description' => '낮은 우선순위',
                'status' => 'pending',
                'priority' => 'low',
                'requester_id' => $userId,
                'required_by' => $baseDate->copy()->addDays(4)->format('Y-m-d'),
                'estimated_hours' => 8,
                'completed_percentage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // When: 캘린더 데이터 로드
        $component = Livewire::test(CalendarLivewire::class)
            ->call('loadCalendarData');

        // Then: 우선순위별 색상 매핑 확인
        $requests = $component->get('requests');
        $this->assertCount(4, $requests);

        foreach ($requests as $request) {
            switch ($request['priority']) {
                case 'urgent':
                    $this->assertEquals('red', $request['color']);
                    break;
                case 'high':
                    $this->assertEquals('orange', $request['color']);
                    break;
                case 'medium':
                    $this->assertEquals('blue', $request['color']);
                    break;
                case 'low':
                    $this->assertEquals('gray', $request['color']);
                    break;
            }
        }
    }
}