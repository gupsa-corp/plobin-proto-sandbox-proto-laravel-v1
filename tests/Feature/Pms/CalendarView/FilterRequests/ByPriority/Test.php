<?php

namespace Tests\Feature\Pms\CalendarView\FilterRequests\ByPriority;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_우선순위_필터가_적용된다(): void
    {
        $userId = DB::table('plobin_users')->insertGetId([
            'name' => '테스트 사용자', 'email' => 'test@example.com',
            'role' => 'analyst', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('plobin_analysis_requests')->insert([
            ['title' => '긴급 요청', 'description' => '긴급 분석 요청입니다.', 'status' => 'pending', 'priority' => 'urgent', 'requester_id' => $userId, 'required_by' => now()->addDays(1)->format('Y-m-d'), 'estimated_hours' => 2, 'completed_percentage' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['title' => '낮은 요청', 'description' => '낮은 우선순위 분석 요청입니다.', 'status' => 'pending', 'priority' => 'low', 'requester_id' => $userId, 'required_by' => now()->addDays(2)->format('Y-m-d'), 'estimated_hours' => 5, 'completed_percentage' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $component = Livewire::test(CalendarLivewire::class)
            ->set('filterPriority', 'urgent')
            ->call('loadCalendarData');

        $requests = $component->get('requests');
        $this->assertNotEmpty($requests);
        foreach ($requests as $request) {
            $this->assertEquals('urgent', $request['priority']);
        }
    }
}
