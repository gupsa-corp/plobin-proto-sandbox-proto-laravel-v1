<?php

namespace Tests\Feature\Pms\Dashboard\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_PMS_대시보드_페이지가_정상_렌더링된다(): void
    {
        // Given: PMS 대시보드 페이지가 준비되어 있고
        
        // When: 페이지를 방문하면
        $response = $this->get('/pms/dashboard');
        
        // Then: 성공적으로 렌더링된다
        $response->assertStatus(200)
                ->assertSee('대시보드')
                ->assertSee('PMS');
                
        // And: 필요한 UI 요소들이 포함되어 있다
        $response->assertSee('전체 프로젝트')
                ->assertSee('프로젝트 관리 시스템');
    }
}