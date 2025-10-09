<?php

namespace Tests\Feature\Pms\Projects\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_프로젝트_목록_조회가_성공한다(): void
    {
        // Given: API 엔드포인트가 준비되어 있고
        $endpoint = '/api/pms/projects';
        
        // When: 프로젝트 목록을 요청하면
        $response = $this->getJson($endpoint);
        
        // Then: 성공 응답을 받는다
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message', 
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'status',
                            'priority',
                            'progress',
                            'startDate',
                            'endDate',
                            'team',
                            'createdAt'
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
                
        // And: 프로젝트 데이터가 포함되어 있다
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
    }
}