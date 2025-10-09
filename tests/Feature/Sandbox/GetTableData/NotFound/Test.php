<?php

namespace Tests\Feature\Sandbox\GetTableData\NotFound;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Sandbox\GetTableData\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_존재하지_않는_테이블_데이터_조회_시_에러가_반환된다(): void
    {
        // Given: 존재하지 않는 테이블 ID
        $service = new Service();
        $nonExistentTableId = 999;
        
        // When: 존재하지 않는 테이블 데이터 조회
        $result = $service->execute($nonExistentTableId);
        
        // Then: 실패 응답 확인
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('Table not found', $result['message']);
    }
}