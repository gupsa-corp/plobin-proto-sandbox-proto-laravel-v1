<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Plobin_analysis_request;

class PmsCalendarViewTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_캘린더_페이지_로드_및_기본_요소_표시(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    ->assertSee('캘린더 뷰')
                    ->assertSee('프로젝트 일정을 캘린더로 확인하세요')
                    ->assertPresent('[wire:click="openCreateModal"]')
                    ->assertSee('일정 추가')
                    ->assertSee('필터')
                    ->assertSee('월별')
                    ->assertSee('주별');
        });
    }

    public function test_뷰_모드_전환_기능(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    // 주별 뷰로 전환
                    ->click('[wire:click="changeViewMode(\'week\')"]')
                    ->pause(500)
                    ->assertSeeIn('.bg-blue-600', '주별')
                    // 다시 월별 뷰로 전환
                    ->click('[wire:click="changeViewMode(\'month\')"]')
                    ->pause(500)
                    ->assertSeeIn('.bg-blue-600', '월별');
        });
    }

    public function test_필터_패널_토글_기능(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    // 필터 버튼 클릭하여 패널 열기
                    ->click('button[wire:click="$toggle(\'showFilters\')"]')
                    ->pause(300)
                    ->assertSee('우선순위')
                    ->assertSee('상태')
                    ->assertSee('필터 초기화');
        });
    }

    public function test_일정_추가_모달_열기(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    // 일정 추가 버튼 클릭
                    ->click('[wire:click="openCreateModal"]')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertSee('새 일정 추가')
                    ->assertPresent('input[wire:model="eventForm.title"]')
                    ->assertPresent('textarea[wire:model="eventForm.description"]')
                    ->assertPresent('input[wire:model="eventForm.date"]')
                    ->assertPresent('input[wire:model="eventForm.estimated_hours"]')
                    ->assertPresent('select[wire:model="eventForm.priority"]');
        });
    }

    public function test_일정_추가_모달_취소_버튼(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    ->click('[wire:click="openCreateModal"]')
                    ->waitFor('.fixed.inset-0', 3)
                    // 취소 버튼 클릭
                    ->click('button[wire:click="closeCreateModal"]')
                    ->pause(300)
                    ->assertDontSee('새 일정 추가');
        });
    }

    public function test_새_일정_생성_플로우(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    ->click('[wire:click="openCreateModal"]')
                    ->waitFor('.fixed.inset-0', 3)
                    // 폼 입력
                    ->type('input[wire:model="eventForm.title"]', '테스트 분석 요청')
                    ->type('textarea[wire:model="eventForm.description"]', '이것은 E2E 테스트용 상세 설명입니다. 최소 10자 이상 작성합니다.')
                    ->type('input[wire:model="eventForm.date"]', now()->addDays(3)->format('Y-m-d'))
                    ->type('input[wire:model="eventForm.estimated_hours"]', '5')
                    ->select('select[wire:model="eventForm.priority"]', 'high')
                    // 생성 버튼 클릭
                    ->press('생성')
                    ->waitUntilMissing('.fixed.inset-0', 5)
                    ->pause(1000)
                    ->assertSee('분석 요청이 성공적으로 생성되었습니다.');
        });

        // 데이터베이스에 저장되었는지 확인
        $this->assertDatabaseHas('plobin_analysis_requests', [
            'title' => '테스트 분석 요청',
            'priority' => 'high',
        ]);
    }

    public function test_날짜_탐색_기능(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    // 다음 월로 이동
                    ->click('button[wire:click="nextPeriod"]')
                    ->pause(500)
                    // 이전 월로 이동
                    ->click('button[wire:click="previousPeriod"]')
                    ->pause(500)
                    // 오늘로 이동
                    ->click('button[wire:click="goToToday"]')
                    ->pause(500)
                    ->assertSee('오늘');
        });
    }

    public function test_필터_적용_기능(): void
    {
        $user = User::factory()->create();

        // 테스트 데이터 생성
        Plobin_analysis_request::factory()->create([
            'requester_id' => $user->id,
            'title' => '높은 우선순위 작업',
            'priority' => 'high',
            'status' => 'in_progress',
            'required_by' => now()->format('Y-m-d'),
        ]);

        Plobin_analysis_request::factory()->create([
            'requester_id' => $user->id,
            'title' => '낮은 우선순위 작업',
            'priority' => 'low',
            'status' => 'pending',
            'required_by' => now()->format('Y-m-d'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    // 필터 패널 열기
                    ->click('button[wire:click="$toggle(\'showFilters\')"]')
                    ->pause(300)
                    // 우선순위 필터 적용
                    ->select('select[wire:model.live="filterPriority"]', 'high')
                    ->pause(1000)
                    ->assertSee('높은 우선순위 작업')
                    // 필터 초기화
                    ->click('button[wire:click="clearFilters"]')
                    ->pause(1000);
        });
    }

    public function test_캘린더_요일_헤더_표시(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    ->assertSee('일')
                    ->assertSee('월')
                    ->assertSee('화')
                    ->assertSee('수')
                    ->assertSee('목')
                    ->assertSee('금')
                    ->assertSee('토');
        });
    }

    public function test_캘린더_범례_표시(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/pms/calendar')
                    ->assertSee('범례 및 사용법')
                    ->assertSee('우선순위')
                    ->assertSee('사용법')
                    ->assertSee('날짜 클릭: 해당 날짜 일정 보기')
                    ->assertSee('날짜 더블클릭: 새 일정 추가');
        });
    }
}
