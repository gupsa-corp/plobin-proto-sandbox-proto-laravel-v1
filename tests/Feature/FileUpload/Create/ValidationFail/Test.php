<?php

namespace Tests\Feature\FileUpload\Create\ValidationFail;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_필수_파일_누락_시_유효성_검사가_실패한다(): void
    {
        // Given: 파일 없이 요청 데이터가 준비되어 있고
        $requestData = [
            'title' => '테스트 문서',
            'description' => '테스트용 문서입니다.',
            'category' => 'document'
            // 'file' 필드 누락
        ];
        
        // When: 파일 업로드 API를 호출하면
        $response = $this->postJson('/api/file-upload/create', $requestData);
        
        // Then: 유효성 검사 실패 응답을 받는다
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ])
                ->assertJson([
                    'success' => false
                ]);
                
        // And: 에러 메시지가 포함되어 있다
        $responseData = $response->json();
        $this->assertStringContainsString('파일', $responseData['message']);
    }
}