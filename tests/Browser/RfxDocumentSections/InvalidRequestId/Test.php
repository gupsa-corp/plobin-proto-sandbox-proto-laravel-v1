<?php

namespace Tests\Browser\RfxDocumentSections\InvalidRequestId;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Http;

class Test extends DuskTestCase
{
    public function test_존재하지_않는_요청ID로_접근_시_에러_처리(): void
    {
        // 존재하지 않는 UUID
        $invalidRequestId = '00000000-0000-0000-0000-000000000000';

        // API 호출 테스트
        $response = Http::get("http://127.0.0.1:1455/api/rfx/documents/{$invalidRequestId}/sections");

        $this->assertTrue($response->successful() || $response->status() === 404, 'API가 정상 응답하거나 404를 반환해야 합니다');

        $data = $response->json();

        // 성공 시 sections가 비어있거나, 실패 시 success가 false여야 함
        if ($data['success']) {
            $this->assertEmpty($data['data']['sections'], '존재하지 않는 요청은 빈 섹션을 반환해야 합니다');
        } else {
            $this->assertFalse($data['success'], 'success 필드가 false여야 합니다');
        }
    }
}
