<?php

namespace Tests\Feature\DocumentAnalysis\Create\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_문서_분석_요청이_성공한다(): void
    {
        // Given: 분석 요청 데이터가 준비되어 있고
        $requestData = [
            'file_id' => 1,
            'analysis_type' => 'summary',
            'options' => [
                'language' => 'ko',
                'detail_level' => 'medium'
            ]
        ];
        
        // When: 문서 분석 API를 호출하면
        $response = $this->postJson('/api/document-analysis/create', $requestData);
        
        // Then: 성공 응답을 받는다
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'analysis_id',
                        'file_id',
                        'analysis_type',
                        'status',
                        'created_at'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => '문서 분석 요청이 성공적으로 처리되었습니다.'
                ]);
                
        // And: 응답 데이터가 올바르다
        $data = $response->json('data');
        $this->assertIsInt($data['analysis_id']);
        $this->assertEquals(1, $data['file_id']);
        $this->assertEquals('summary', $data['analysis_type']);
        $this->assertEquals('processing', $data['status']);
        $this->assertNotEmpty($data['created_at']);
    }
}