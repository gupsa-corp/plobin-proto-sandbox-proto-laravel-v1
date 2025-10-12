<?php

namespace Tests\Feature\Rfx\DocumentSections\GetList\InvalidRequestId;

use Tests\TestCase;

class Test extends TestCase
{
    public function test_존재하지_않는_요청ID로_접근_시_에러_처리(): void
    {
        // 존재하지 않는 UUID
        $invalidRequestId = '00000000-0000-0000-0000-000000000000';

        $response = $this->getJson("/api/rfx/documents/{$invalidRequestId}/sections");

        // API가 정상 응답하거나 404를 반환해야 함
        $this->assertTrue(
            in_array($response->status(), [200, 404]),
            'API가 200 또는 404 상태 코드를 반환해야 합니다'
        );

        if ($response->status() === 200) {
            $data = $response->json('data');
            // 존재하지 않는 요청은 빈 섹션을 반환하거나 에러를 표시해야 함
            $this->assertTrue(
                empty($data['sections']) || !$response->json('success'),
                '존재하지 않는 요청은 빈 섹션을 반환하거나 success=false여야 합니다'
            );
        }
    }
}
