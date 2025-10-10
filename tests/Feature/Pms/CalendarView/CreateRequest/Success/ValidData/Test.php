<?php

namespace Tests\Feature\Pms\CalendarView\CreateRequest\Success\ValidData;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_유효한_데이터로_분석_요청이_성공적으로_생성된다(): void
    {
        // Given: 테스트 사용자 생성
        $userId = DB::table('plobin_users')->insertGetId([
            'name' => '요청자',
            'email' => 'requester@example.com',
            'role' => 'analyst',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // When: Livewire 컴포넌트에서 분석 요청 생성
        Livewire::test(CalendarLivewire::class)
            ->set('eventForm.title', '테스트 분석 요청')
            ->set('eventForm.description', 'E2E 테스트를 위한 샘플 분석 요청입니다.')
            ->set('eventForm.date', now()->addDays(7)->format('Y-m-d'))
            ->set('eventForm.estimated_hours', 5)
            ->set('eventForm.priority', 'high')
            ->call('createEvent')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        // Then: DB에 레코드 생성 확인
        $this->assertDatabaseHas('plobin_analysis_requests', [
            'title' => '테스트 분석 요청',
            'description' => 'E2E 테스트를 위한 샘플 분석 요청입니다.',
            'status' => 'pending',
            'priority' => 'high',
            'estimated_hours' => 5,
            'completed_percentage' => 0,
        ]);
    }
}
