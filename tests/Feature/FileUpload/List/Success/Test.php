<?php

namespace Tests\Feature\FileUpload\List\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_파일_목록_조회가_성공한다(): void
    {
        // Given: API 엔드포인트가 준비되어 있고
        $endpoint = '/api/file-upload/list';
        
        // When: 파일 목록을 요청하면
        $response = $this->getJson($endpoint);
        
        // Then: 성공 응답을 받는다
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'files' => [
                            '*' => [
                                'id',
                                'original_name',
                                'file_size',
                                'mime_type',
                                'upload_date',
                                'title',
                                'description',
                                'category'
                            ]
                        ],
                        'total_count',
                        'total_size'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
                
        // And: 파일 목록 데이터가 올바른 형식이다
        $data = $response->json('data');
        $this->assertIsArray($data['files']);
        $this->assertIsInt($data['total_count']);
        $this->assertIsString($data['total_size']);
        
        // And: 각 파일 정보가 올바른 형식이다
        foreach ($data['files'] as $file) {
            $this->assertIsInt($file['id']);
            $this->assertIsString($file['original_name']);
            $this->assertIsInt($file['file_size']);
            $this->assertIsString($file['mime_type']);
            $this->assertIsString($file['upload_date']);
        }
    }
}