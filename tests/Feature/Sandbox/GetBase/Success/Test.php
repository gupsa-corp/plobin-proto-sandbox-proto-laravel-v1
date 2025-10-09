<?php

namespace Tests\Feature\Sandbox\BaseManager\GetBase;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxBase;
use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxField;
use App\Models\Plobin\SandboxView;
use App\Services\Sandbox\GetBase\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_베이스_상세_조회가_성공한다(): void
    {
        // Given: 완전한 베이스 구조 생성
        $base = SandboxBase::create([
            'name' => '고객 관리 시스템',
            'slug' => 'customer-management',
            'description' => '고객 정보를 관리하는 시스템',
            'icon' => '👥',
            'color' => '#4CAF50',
            'created_by' => 1
        ]);

        $table = SandboxTable::create([
            'base_id' => $base->id,
            'name' => '고객',
            'slug' => 'customers',
            'description' => '고객 정보 테이블',
            'icon' => '👤',
            'color' => '#2196F3',
            'sort_order' => 1
        ]);

        // 필드 생성
        $nameField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '고객명',
            'slug' => 'customer_name',
            'field_type' => 'singleLineText',
            'is_required' => true,
            'is_primary' => true,
            'sort_order' => 1
        ]);

        $emailField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '이메일',
            'slug' => 'email',
            'field_type' => 'email',
            'is_required' => false,
            'sort_order' => 2
        ]);

        $statusField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '상태',
            'slug' => 'status',
            'field_type' => 'singleSelect',
            'field_config' => [
                'options' => [
                    ['name' => '활성', 'color' => '#4CAF50'],
                    ['name' => '비활성', 'color' => '#F44336']
                ]
            ],
            'sort_order' => 3
        ]);

        // 뷰 생성
        $gridView = SandboxView::create([
            'table_id' => $table->id,
            'name' => '전체 고객',
            'view_type' => 'grid',
            'is_default' => true,
            'created_by' => 1
        ]);

        $kanbanView = SandboxView::create([
            'table_id' => $table->id,
            'name' => '상태별 칸반',
            'view_type' => 'kanban',
            'group_config' => ['field_slug' => 'status'],
            'created_by' => 1
        ]);

        // When: 베이스 상세 조회
        $service = new Service();
        $result = $service->execute($base->id);

        // Then: 완전한 구조가 반환되는지 확인
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        
        $baseData = $result['data'];
        
        // 베이스 정보 확인
        $this->assertEquals('고객 관리 시스템', $baseData['name']);
        $this->assertEquals('customer-management', $baseData['slug']);
        $this->assertEquals('👥', $baseData['icon']);
        
        // 테이블 정보 확인
        $this->assertArrayHasKey('tables', $baseData);
        $this->assertCount(1, $baseData['tables']);
        
        $tableData = $baseData['tables']->first();
        $this->assertEquals('고객', $tableData['name']);
        $this->assertEquals('customers', $tableData['slug']);
        
        // 필드 정보 확인
        $this->assertArrayHasKey('fields', $tableData);
        $this->assertCount(3, $tableData['fields']);
        
        $fields = $tableData['fields'];
        $nameFieldData = $fields->where('slug', 'customer_name')->first();
        $this->assertEquals('고객명', $nameFieldData['name']);
        $this->assertEquals('singleLineText', $nameFieldData['field_type']);
        $this->assertTrue($nameFieldData['is_required']);
        $this->assertTrue($nameFieldData['is_primary']);
        
        $statusFieldData = $fields->where('slug', 'status')->first();
        $this->assertEquals('singleSelect', $statusFieldData['field_type']);
        $this->assertArrayHasKey('options', $statusFieldData['field_config']);
        
        // 뷰 정보 확인
        $this->assertArrayHasKey('views', $tableData);
        $this->assertCount(2, $tableData['views']);
        
        $views = $tableData['views'];
        $gridViewData = $views->where('view_type', 'grid')->first();
        $this->assertEquals('전체 고객', $gridViewData['name']);
        $this->assertTrue($gridViewData['is_default']);
        
        $kanbanViewData = $views->where('view_type', 'kanban')->first();
        $this->assertEquals('상태별 칸반', $kanbanViewData['name']);
        $this->assertFalse($kanbanViewData['is_default']);
    }
}