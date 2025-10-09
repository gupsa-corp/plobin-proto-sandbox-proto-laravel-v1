<?php

namespace Tests\Feature\Sandbox\GetBase\NotFound;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Sandbox\GetBase\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_존재하지_않는_베이스_조회_시_에러가_반환된다(): void
    {
        // Given: 존재하지 않는 베이스 ID
        $service = new Service();
        $nonExistentBaseId = 999;
        
        // When: 존재하지 않는 베이스 조회
        $result = $service->execute($nonExistentBaseId);
        
        // Then: 실패 응답 확인
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('Base not found', $result['message']);
    }
}