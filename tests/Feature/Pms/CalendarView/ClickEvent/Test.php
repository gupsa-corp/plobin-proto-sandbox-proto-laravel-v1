<?php

namespace Tests\Feature\Pms\CalendarView\ClickEvent;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_일정_데이터에_description_필드가_포함되어야_한다(): void
    {
        // 테스트 데이터 생성
        DB::table('plobin_users')->insert([
            'id' => 1,
            'name' => '테스트 사용자',
            'email' => 'test@example.com',
            'role' => 'analyst',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('plobin_analysis_requests')->insert([
            'title' => '테스트 분석 요청',
            'description' => '테스트 설명입니다.',
            'status' => 'pending',
            'priority' => 'high',
            'required_by' => now()->addDays(7)->format('Y-m-d'),
            'estimated_hours' => 10,
            'completed_percentage' => 0,
            'requester_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Livewire 컴포넌트 테스트
        $component = \Livewire\Livewire::test(\App\Livewire\Pms\CalendarView\Livewire::class);

        // requests 배열에 데이터가 있는지 확인
        $this->assertNotEmpty($component->get('requests'));

        // 각 요청 데이터에 description 필드가 있는지 확인
        foreach ($component->get('requests') as $request) {
            $this->assertArrayHasKey('description', $request,
                '일정 데이터에 description 필드가 누락되었습니다.');
            $this->assertArrayHasKey('title', $request);
            $this->assertArrayHasKey('status', $request);
            $this->assertArrayHasKey('priority', $request);
            $this->assertArrayHasKey('requester', $request);
        }
    }
}
