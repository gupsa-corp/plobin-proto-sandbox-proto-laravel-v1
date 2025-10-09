<?php

namespace Tests\Feature\Sandbox\BaseManager\ListBases;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxBase;
use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxRecord;
use App\Services\Sandbox\ListBases\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_베이스_목록_조회가_성공한다(): void
    {
        // Given: 테스트 베이스와 테이블 생성
        $base = SandboxBase::create([
            'name' => '테스트 베이스',
            'slug' => 'test-base',
            'description' => '테스트용 베이스',
            'icon' => '🧪',
            'color' => '#FF5733',
            'created_by' => 1
        ]);

        $table = SandboxTable::create([
            'base_id' => $base->id,
            'name' => '테스트 테이블',
            'slug' => 'test-table',
            'description' => '테스트용 테이블',
            'icon' => '📋',
            'color' => '#33C3F0',
            'sort_order' => 1
        ]);

        // 테스트 레코드 3개 생성
        for ($i = 1; $i <= 3; $i++) {
            SandboxRecord::create([
                'table_id' => $table->id,
                'record_number' => "TST-{$i}",
                'created_by' => 1
            ]);
        }

        // When: 베이스 목록 조회 서비스 실행
        $service = new Service();
        $result = $service->execute();

        // Then: 올바른 구조로 데이터가 반환되는지 확인
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        
        $bases = $result['data'];
        $this->assertCount(1, $bases);
        
        $returnedBase = $bases->first();
        $this->assertEquals('테스트 베이스', $returnedBase['name']);
        $this->assertEquals('test-base', $returnedBase['slug']);
        $this->assertEquals('🧪', $returnedBase['icon']);
        $this->assertEquals('#FF5733', $returnedBase['color']);
        
        // 테이블 정보 확인
        $this->assertArrayHasKey('tables', $returnedBase);
        $tables = $returnedBase['tables'];
        $this->assertCount(1, $tables);
        
        $returnedTable = $tables->first();
        $this->assertEquals('테스트 테이블', $returnedTable['name']);
        $this->assertEquals('test-table', $returnedTable['slug']);
        $this->assertEquals(3, $returnedTable['record_count']);
    }
}