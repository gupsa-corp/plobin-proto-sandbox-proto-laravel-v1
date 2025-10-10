<?php

namespace Tests\Browser\PmsCalendarView\WeekViewClick\SwitchViewMode;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Test extends DuskTestCase
{
    public function test_월별_보기와_주별_보기_전환_후_클릭이_정상_작동한다(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/calendar')
                    ->waitFor('.grid-cols-7', 5)
                    // 월별 보기에서 클릭 확인
                    ->click('[title*="새로운 테스트 일정"]')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertSee('새로운 테스트 일정')
                    ->click('button:has-text("닫기")')
                    ->pause(500)
                    // 주별 보기로 전환
                    ->click('button:has-text("주별")')
                    ->pause(500)
                    // 주별 보기에서 클릭 확인
                    ->click('.cursor-pointer:has-text("새로운 테스트 일정")')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertSee('새로운 테스트 일정')
                    ->click('button:has-text("닫기")')
                    ->pause(500)
                    ->assertMissing('.fixed.inset-0');
        });
    }
}
