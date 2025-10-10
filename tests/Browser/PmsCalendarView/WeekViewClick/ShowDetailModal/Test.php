<?php

namespace Tests\Browser\PmsCalendarView\WeekViewClick\ShowDetailModal;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Test extends DuskTestCase
{
    public function test_주별_보기에서_일정_클릭_시_상세_모달이_표시된다(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/calendar')
                    ->waitFor('.grid-cols-7', 5)
                    // 주별 보기로 전환
                    ->click('button:has-text("주별")')
                    ->pause(500)
                    ->assertVisible('.grid-cols-8') // 주별 보기 그리드 확인
                    // 주별 보기의 일정 클릭
                    ->click('.cursor-pointer:has-text("새로운 테스트 일정")')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertSee('새로운 테스트 일정')
                    ->assertSee('우선순위')
                    ->assertSee('상태')
                    ->assertSee('요청자')
                    ->assertSee('담당자')
                    ->assertSee('시작일')
                    ->assertSee('종료일')
                    ->assertSee('예상 소요시간');
        });
    }
}
