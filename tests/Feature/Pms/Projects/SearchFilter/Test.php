<?php

namespace Tests\Feature\Pms\Projects\SearchFilter;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_프로젝트_검색_필터가_정상_작동한다(): void
    {
        // Given: 검색어를 포함한 요청 파라미터가 준비되어 있고
        $searchTerm = '웹사이트';
        $endpoint = '/api/pms/projects';
        $params = [
            'search' => $searchTerm,
            'status' => 'in_progress',
            'priority' => 'high'
        ];
        
        // When: 필터링된 프로젝트 목록을 요청하면
        $response = $this->getJson($endpoint . '?' . http_build_query($params));
        
        // Then: 성공 응답을 받는다
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
                
        // And: 필터링된 결과만 반환된다
        $data = $response->json('data');
        $this->assertIsArray($data);
        
        // And: 반환된 모든 프로젝트는 검색 조건을 만족한다
        foreach ($data as $project) {
            $this->assertTrue(
                str_contains(strtolower($project['name']), strtolower($searchTerm)) ||
                str_contains(strtolower($project['description']), strtolower($searchTerm))
            );
            $this->assertEquals('in_progress', $project['status']);
            $this->assertEquals('high', $project['priority']);
        }
    }
}