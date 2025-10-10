<?php

namespace Tests\Browser\PmsCalendarView\WeekViewClick\CloseModal;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Test extends DuskTestCase
{
    public function test_주별_보기에서_모달_닫기_버튼이_정상_작동한다(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/calendar')
                    ->waitFor('.grid-cols-7', 5)
                    // 주별 보기로 전환
                    ->click('button:has-text("주별")')
                    ->pause(500)
                    // 일정 클릭하여 모달 열기
                    ->click('.cursor-pointer:has-text("새로운 테스트 일정")')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertVisible('.fixed.inset-0')
                    // 닫기 버튼 클릭
                    ->click('button:has-text("닫기")')
                    ->pause(500)
                    ->assertMissing('.fixed.inset-0');
        });
    }
}
