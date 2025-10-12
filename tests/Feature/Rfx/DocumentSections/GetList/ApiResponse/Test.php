<?php

namespace Tests\Feature\Rfx\DocumentSections\GetList\ApiResponse;

use Tests\TestCase;

class Test extends TestCase
{
    public function test_API_응답이_정상적으로_반환됨(): void
    {
        // 실제 존재하는 OCR 요청 ID 사용
        $requestId = '0199d940-9d0e-71ba-bf78-8d6b1220633a';

        $response = $this->getJson("/api/rfx/documents/{$requestId}/sections");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'document_id',
                         'document_name',
                         'page_number',
                         'sections',
                         'statistics' => [
                             'total_sections',
                             'total_subsections',
                             'total_blocks'
                         ]
                     ]
                 ]);

        // 섹션이 최소 1개 이상 있어야 함
        $data = $response->json('data');
        $this->assertGreaterThan(0, count($data['sections']), '최소 1개 이상의 섹션이 있어야 합니다');
    }
}
