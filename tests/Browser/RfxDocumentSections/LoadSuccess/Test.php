<?php

namespace Tests\Browser\RfxDocumentSections\LoadSuccess;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Test extends DuskTestCase
{
    public function test_문서_섹션_페이지_로드_성공(): void
    {
        $this->browse(function (Browser $browser) {
            // 실제 존재하는 OCR 요청 ID 사용
            $requestId = '0199d940-9d0e-71ba-bf78-8d6b1220633a';

            $browser->visit("/rfx/documents/{$requestId}/sections")
                    ->waitForText('기타', 5) // 섹션 제목이 나타날 때까지 5초 대기
                    ->assertSee('문서 섹션')
                    ->assertSee('기타') // 기본 섹션 제목
                    ->assertDontSee('섹션이 없습니다'); // 에러 메시지가 없어야 함
        });
    }
}
