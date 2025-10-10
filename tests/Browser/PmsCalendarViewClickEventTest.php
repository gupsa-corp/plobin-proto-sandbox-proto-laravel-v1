<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PmsCalendarViewClickEventTest extends DuskTestCase
{
    public function test_일정_클릭_시_상세_모달이_표시된다(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/calendar')
                    ->waitFor('.grid-cols-7', 5)
                    // 첫 번째 일정 카드 클릭
                    ->click('[title*="보안 취약점 분석"]')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertSee('보안 취약점 분석')
                    ->assertSee('상세 설명')
                    ->assertSee('우선순위')
                    ->assertSee('상태')
                    ->assertSee('요청자')
                    ->assertSee('담당자')
                    ->assertSee('완료 요청일')
                    ->assertSee('예상 소요시간')
                    ->assertSee('진행률');
        });
    }

    public function test_상세_모달의_닫기_버튼이_정상_작동한다(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/calendar')
                    ->waitFor('.grid-cols-7', 5)
                    // 일정 클릭하여 모달 열기
                    ->click('[title*="보안 취약점 분석"]')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertVisible('.fixed.inset-0')
                    // 닫기 버튼 클릭
                    ->click('button:has-text("닫기")')
                    ->pause(500)
                    ->assertMissing('.fixed.inset-0');
        });
    }

    public function test_여러_일정을_순차적으로_클릭할_수_있다(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/calendar')
                    ->waitFor('.grid-cols-7', 5)
                    // 첫 번째 일정 클릭
                    ->click('[title*="보안 취약점 분석"]')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertSee('보안 취약점 분석')
                    ->click('button:has-text("닫기")')
                    ->pause(500)
                    // 두 번째 일정 클릭
                    ->click('[title*="데이터베이스 쿼리 최적화"]')
                    ->waitFor('.fixed.inset-0', 3)
                    ->assertSee('데이터베이스 쿼리 최적화')
                    ->click('button:has-text("닫기")')
                    ->pause(500)
                    ->assertMissing('.fixed.inset-0');
        });
    }
}
