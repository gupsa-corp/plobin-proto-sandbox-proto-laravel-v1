<?php

namespace Tests\Feature\Sandbox\GetRecord\NotFound;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Sandbox\GetRecord\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_존재하지_않는_레코드_조회_시_에러가_반환된다(): void
    {
        // Given: 존재하지 않는 레코드 ID
        $service = new Service();
        $nonExistentRecordId = 999;
        
        // When: 존재하지 않는 레코드 조회
        $result = $service->execute($nonExistentRecordId);
        
        // Then: 실패 응답 확인
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('Record not found', $result['message']);
    }
}