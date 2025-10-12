<?php

namespace Tests\Browser\RfxDocumentSections\ApiResponse;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Http;

class Test extends DuskTestCase
{
    public function test_API_응답이_정상적으로_반환됨(): void
    {
        // 실제 존재하는 OCR 요청 ID 사용
        $requestId = '0199d940-9d0e-71ba-bf78-8d6b1220633a';

        // API 직접 호출 테스트
        $response = Http::get("http://127.0.0.1:1455/api/rfx/documents/{$requestId}/sections");

        $this->assertTrue($response->successful(), 'API 응답이 성공해야 합니다');

        $data = $response->json();
        $this->assertTrue($data['success'], 'success 필드가 true여야 합니다');
        $this->assertArrayHasKey('data', $data, 'data 필드가 존재해야 합니다');
        $this->assertArrayHasKey('sections', $data['data'], 'sections 필드가 존재해야 합니다');
        $this->assertGreaterThan(0, count($data['data']['sections']), '최소 1개 이상의 섹션이 있어야 합니다');
    }
}
