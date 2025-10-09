<?php

namespace Tests\Feature\Sandbox\BaseManager\GetRecord;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxBase;
use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxField;
use App\Models\Plobin\SandboxRecord;
use App\Models\Plobin\SandboxFieldValue;
use App\Services\Sandbox\GetRecord\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_개별_레코드_조회가_성공한다(): void
    {
        // Given: 테이블과 상세 레코드 생성
        $base = SandboxBase::create([
            'name' => '재고 관리',
            'slug' => 'inventory',
            'created_by' => 1
        ]);

        $table = SandboxTable::create([
            'base_id' => $base->id,
            'name' => '상품',
            'slug' => 'products',
            'icon' => '📦',
            'color' => '#9C27B0',
            'sort_order' => 1
        ]);

        // 다양한 필드 타입으로 필드 생성
        $nameField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '상품명',
            'slug' => 'product_name',
            'field_type' => 'singleLineText',
            'is_primary' => true,
            'sort_order' => 1
        ]);

        $priceField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '가격',
            'slug' => 'price',
            'field_type' => 'currency',
            'sort_order' => 2
        ]);

        $discountField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '할인율',
            'slug' => 'discount',
            'field_type' => 'percent',
            'sort_order' => 3
        ]);

        $categoryField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '카테고리',
            'slug' => 'category',
            'field_type' => 'multipleSelect',
            'field_config' => [
                'options' => [
                    ['name' => '전자제품', 'color' => '#2196F3'],
                    ['name' => '의류', 'color' => '#E91E63'],
                    ['name' => '식품', 'color' => '#4CAF50']
                ]
            ],
            'sort_order' => 4
        ]);

        $websiteField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '제품 페이지',
            'slug' => 'website',
            'field_type' => 'url',
            'sort_order' => 5
        ]);

        $availableField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '재고 있음',
            'slug' => 'available',
            'field_type' => 'checkbox',
            'sort_order' => 6
        ]);

        // 테스트 레코드 생성
        $record = SandboxRecord::create([
            'table_id' => $table->id,
            'record_number' => 'PROD-001',
            'created_by' => 1
        ]);

        // 각 필드 타입별 값 설정
        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $nameField->id,
            'value_text' => '스마트폰 갤럭시 S24'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $priceField->id,
            'value_number' => 1200000
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $discountField->id,
            'value_number' => 15
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $categoryField->id,
            'value_json' => ['전자제품']
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $websiteField->id,
            'value_text' => 'https://example.com/galaxy-s24'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $availableField->id,
            'value_boolean' => true
        ]);

        // When: 레코드 상세 조회
        $service = new Service();
        $result = $service->execute($record->id);

        // Then: 완전한 레코드 데이터 구조 확인
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        
        $recordData = $result['data'];
        
        // 기본 레코드 정보 확인
        $this->assertEquals($record->id, $recordData['id']);
        $this->assertEquals('PROD-001', $recordData['record_number']);
        
        // 테이블 정보 확인
        $this->assertArrayHasKey('table', $recordData);
        $this->assertEquals('상품', $recordData['table']['name']);
        $this->assertEquals($table->id, $recordData['table']['id']);
        
        // 필드별 값과 표시값 확인
        $this->assertArrayHasKey('fields', $recordData);
        $fields = $recordData['fields'];
        
        // 텍스트 필드
        $this->assertEquals('스마트폰 갤럭시 S24', $fields['product_name']['value']);
        $this->assertEquals('스마트폰 갤럭시 S24', $fields['product_name']['display_value']);
        $this->assertEquals('singleLineText', $fields['product_name']['field_type']);
        
        // 통화 필드
        $this->assertEquals(1200000, $fields['price']['value']);
        $this->assertEquals('₩1,200,000', $fields['price']['display_value']);
        $this->assertEquals('currency', $fields['price']['field_type']);
        
        // 퍼센트 필드
        $this->assertEquals(15, $fields['discount']['value']);
        $this->assertEquals('15%', $fields['discount']['display_value']);
        $this->assertEquals('percent', $fields['discount']['field_type']);
        
        // 다중 선택 필드
        $this->assertEquals(['전자제품'], $fields['category']['value']);
        $this->assertEquals('전자제품', $fields['category']['display_value']);
        $this->assertEquals('multipleSelect', $fields['category']['field_type']);
        
        // URL 필드
        $this->assertEquals('https://example.com/galaxy-s24', $fields['website']['value']);
        $this->assertEquals('https://example.com/galaxy-s24', $fields['website']['display_value']);
        $this->assertEquals('url', $fields['website']['field_type']);
        
        // 체크박스 필드
        $this->assertTrue($fields['available']['value']);
        $this->assertEquals('✓', $fields['available']['display_value']);
        $this->assertEquals('checkbox', $fields['available']['field_type']);
    }
}