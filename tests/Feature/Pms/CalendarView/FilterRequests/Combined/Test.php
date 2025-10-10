<?php

namespace Tests\Feature\Pms\CalendarView\FilterRequests\Combined;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_복합_필터가_적용된다(): void
    {
        $userId = DB::table('plobin_users')->insertGetId([
            'name' => '테스트 사용자', 'email' => 'test@example.com',
            'role' => 'analyst', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('plobin_analysis_requests')->insert(['title' => 'High + Completed', 'description' => '높은 우선순위, 완료', 'status' => 'completed', 'priority' => 'high', 'requester_id' => $userId, 'required_by' => now()->format('Y-m-d'), 'estimated_hours' => 5, 'completed_percentage' => 100, 'completed_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        DB::table('plobin_analysis_requests')->insert(['title' => 'High + Pending', 'description' => '높은 우선순위, 대기', 'status' => 'pending', 'priority' => 'high', 'requester_id' => $userId, 'required_by' => now()->format('Y-m-d'), 'estimated_hours' => 3, 'completed_percentage' => 0, 'completed_at' => null, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('plobin_analysis_requests')->insert(['title' => 'Low + Completed', 'description' => '낮은 우선순위, 완료', 'status' => 'completed', 'priority' => 'low', 'requester_id' => $userId, 'required_by' => now()->format('Y-m-d'), 'estimated_hours' => 2, 'completed_percentage' => 100, 'completed_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        $component = Livewire::test(CalendarLivewire::class)
            ->set('filterPriority', 'high')
            ->set('filterStatus', 'completed')
            ->call('loadCalendarData');

        $requests = $component->get('requests');
        $this->assertNotEmpty($requests);
        foreach ($requests as $request) {
            $this->assertEquals('high', $request['priority']);
            $this->assertEquals('completed', $request['status']);
        }
    }
}
