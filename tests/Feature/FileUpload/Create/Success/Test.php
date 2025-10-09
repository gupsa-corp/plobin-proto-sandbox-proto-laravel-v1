<?php

namespace Tests\Feature\FileUpload\Create\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_파일_업로드가_성공한다(): void
    {
        // Given: 스토리지가 준비되어 있고
        Storage::fake('local');
        
        // And: 업로드할 파일이 준비되어 있고
        $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');
        
        // And: 요청 데이터가 준비되어 있고
        $requestData = [
            'file' => $file,
            'title' => '테스트 문서',
            'description' => '테스트용 PDF 문서입니다.',
            'category' => 'document'
        ];
        
        // When: 파일 업로드 API를 호출하면
        $response = $this->postJson('/api/file-upload/create', $requestData);
        
        // Then: 성공 응답을 받는다
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'original_name',
                        'file_size',
                        'stored_name',
                        'file_path'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => '파일이 성공적으로 업로드되었습니다.'
                ]);
                
        // And: 응답 데이터가 올바르다
        $data = $response->json('data');
        $this->assertIsInt($data['id']);
        $this->assertEquals('test-document.pdf', $data['original_name']);
        $this->assertEquals(1024 * 1024, $data['file_size']); // 1024 KB = 1024*1024 bytes
        $this->assertNotEmpty($data['stored_name']);
        $this->assertNotEmpty($data['file_path']);
        
        // And: 파일이 실제로 스토리지에 저장된다
        $this->assertTrue(Storage::disk('public')->exists($data['file_path']));
    }
}