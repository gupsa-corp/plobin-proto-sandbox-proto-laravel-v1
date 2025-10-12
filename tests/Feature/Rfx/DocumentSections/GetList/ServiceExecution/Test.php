<?php

namespace Tests\Feature\Rfx\DocumentSections\GetList\ServiceExecution;

use Tests\TestCase;
use App\Services\Rfx\DocumentSections\GetList\Service;

class Test extends TestCase
{
    public function test_Service가_정상적으로_실행됨(): void
    {
        // 실제 존재하는 OCR 요청 ID 사용
        $requestId = '0199d940-9d0e-71ba-bf78-8d6b1220633a';

        $service = new Service();
        $result = $service->execute($requestId, 1);

        $this->assertTrue($result['success'], 'Service 실행이 성공해야 합니다');
        $this->assertArrayHasKey('data', $result, 'data 필드가 존재해야 합니다');
        $this->assertArrayHasKey('sections', $result['data'], 'sections 필드가 존재해야 합니다');

        // 섹션 데이터 검증
        $this->assertGreaterThan(0, count($result['data']['sections']), '최소 1개 이상의 섹션이 있어야 합니다');

        // 통계 정보 검증
        $stats = $result['data']['statistics'];
        $this->assertArrayHasKey('total_sections', $stats);
        $this->assertArrayHasKey('total_blocks', $stats);
        $this->assertGreaterThan(0, $stats['total_blocks'], '블록 수가 0보다 커야 합니다');
    }
}
