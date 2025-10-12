<?php

namespace Tests\Feature\Rfx\DocumentSections\GetList\OcrApiIntegration;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class Test extends TestCase
{
    public function test_OCR_API와_정상_연동됨(): void
    {
        // OCR API 직접 호출 테스트
        $requestId = '0199d940-9d0e-71ba-bf78-8d6b1220633a';
        $ocrBaseUrl = config('services.ocr.base_url', 'http://localhost:6003');

        $response = Http::timeout(30)->get("{$ocrBaseUrl}/requests/{$requestId}/pages/1/blocks", [
            'limit' => 1000
        ]);

        $this->assertTrue($response->successful(), 'OCR API 응답이 성공해야 합니다');

        $data = $response->json();
        $this->assertArrayHasKey('blocks', $data, 'blocks 필드가 존재해야 합니다');
        $this->assertGreaterThan(0, count($data['blocks']), '최소 1개 이상의 블록이 있어야 합니다');

        // 첫 번째 블록 구조 검증
        $firstBlock = $data['blocks'][0];
        $this->assertArrayHasKey('text', $firstBlock, 'text 필드가 존재해야 합니다');
        $this->assertArrayHasKey('block_type', $firstBlock, 'block_type 필드가 존재해야 합니다');
        $this->assertArrayHasKey('confidence', $firstBlock, 'confidence 필드가 존재해야 합니다');
    }
}
