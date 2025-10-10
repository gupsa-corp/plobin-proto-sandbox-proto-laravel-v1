<?php

namespace Tests\Feature\Pms\CalendarView\FilterRequests\ByStatus;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_상태_필터가_적용된다(): void
    {
        $userId = DB::table('plobin_users')->insertGetId([
            'name' => '테스트 사용자', 'email' => 'test@example.com',
            'role' => 'analyst', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('plobin_analysis_requests')->insert(['title' => '완료된 요청', 'description' => '완료된 분석 요청입니다.', 'status' => 'completed', 'priority' => 'medium', 'requester_id' => $userId, 'required_by' => now()->subDays(1)->format('Y-m-d'), 'estimated_hours' => 3, 'completed_percentage' => 100, 'completed_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        DB::table('plobin_analysis_requests')->insert(['title' => '진행중 요청', 'description' => '진행중인 분석 요청입니다.', 'status' => 'in_progress', 'priority' => 'high', 'requester_id' => $userId, 'required_by' => now()->addDays(3)->format('Y-m-d'), 'estimated_hours' => 8, 'completed_percentage' => 50, 'completed_at' => null, 'created_at' => now(), 'updated_at' => now()]);

        $component = Livewire::test(CalendarLivewire::class)
            ->set('filterStatus', 'completed')
            ->call('loadCalendarData');

        $requests = $component->get('requests');
        $this->assertNotEmpty($requests);
        foreach ($requests as $request) {
            $this->assertEquals('completed', $request['status']);
        }
    }
}
