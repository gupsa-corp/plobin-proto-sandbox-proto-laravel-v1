<?php

namespace Tests\Feature\Sandbox\BaseManager\GetTableData;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxBase;
use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxField;
use App\Models\Plobin\SandboxRecord;
use App\Models\Plobin\SandboxFieldValue;
use App\Services\Sandbox\GetTableData\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_테이블_데이터_조회가_성공한다(): void
    {
        // Given: 완전한 테이블과 데이터 생성
        $base = SandboxBase::create([
            'name' => '프로젝트 관리',
            'slug' => 'project-management',
            'created_by' => 1
        ]);

        $table = SandboxTable::create([
            'base_id' => $base->id,
            'name' => '작업',
            'slug' => 'tasks',
            'icon' => '✅',
            'color' => '#FF9800',
            'sort_order' => 1
        ]);

        // 다양한 필드 타입 생성
        $titleField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '작업명',
            'slug' => 'title',
            'field_type' => 'singleLineText',
            'is_primary' => true,
            'sort_order' => 1
        ]);

        $descriptionField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '설명',
            'slug' => 'description',
            'field_type' => 'multilineText',
            'sort_order' => 2
        ]);

        $statusField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '상태',
            'slug' => 'status',
            'field_type' => 'singleSelect',
            'field_config' => [
                'options' => [
                    ['name' => '대기', 'color' => '#9E9E9E'],
                    ['name' => '진행중', 'color' => '#2196F3'],
                    ['name' => '완료', 'color' => '#4CAF50']
                ]
            ],
            'sort_order' => 3
        ]);

        $priorityField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '우선순위',
            'slug' => 'priority',
            'field_type' => 'rating',
            'sort_order' => 4
        ]);

        $completedField = SandboxField::create([
            'table_id' => $table->id,
            'name' => '완료 여부',
            'slug' => 'completed',
            'field_type' => 'checkbox',
            'sort_order' => 5
        ]);

        // 테스트 레코드 생성
        $record1 = SandboxRecord::create([
            'table_id' => $table->id,
            'record_number' => 'TASK-001',
            'created_by' => 1
        ]);

        $record2 = SandboxRecord::create([
            'table_id' => $table->id,
            'record_number' => 'TASK-002',
            'created_by' => 1
        ]);

        // 첫 번째 레코드 데이터
        SandboxFieldValue::create([
            'record_id' => $record1->id,
            'field_id' => $titleField->id,
            'value_text' => '로그인 기능 개발'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record1->id,
            'field_id' => $descriptionField->id,
            'value_text' => '사용자 로그인 기능을 개발합니다'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record1->id,
            'field_id' => $statusField->id,
            'value_json' => '진행중'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record1->id,
            'field_id' => $priorityField->id,
            'value_number' => 5
        ]);

        SandboxFieldValue::create([
            'record_id' => $record1->id,
            'field_id' => $completedField->id,
            'value_boolean' => false
        ]);

        // 두 번째 레코드 데이터
        SandboxFieldValue::create([
            'record_id' => $record2->id,
            'field_id' => $titleField->id,
            'value_text' => '데이터베이스 설계'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record2->id,
            'field_id' => $statusField->id,
            'value_json' => '완료'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record2->id,
            'field_id' => $priorityField->id,
            'value_number' => 3
        ]);

        SandboxFieldValue::create([
            'record_id' => $record2->id,
            'field_id' => $completedField->id,
            'value_boolean' => true
        ]);

        // When: 테이블 데이터 조회
        $service = new Service();
        $result = $service->execute($table->id);

        // Then: 에어테이블 스타일 데이터 구조 확인
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        
        $data = $result['data'];
        
        // 테이블 정보 확인
        $this->assertArrayHasKey('table', $data);
        $tableInfo = $data['table'];
        $this->assertEquals('작업', $tableInfo['name']);
        $this->assertEquals('tasks', $tableInfo['slug']);
        $this->assertEquals('✅', $tableInfo['icon']);
        
        // 필드 정보 확인
        $this->assertArrayHasKey('fields', $data);
        $this->assertCount(5, $data['fields']);
        
        $fields = $data['fields'];
        $titleFieldInfo = $fields->where('slug', 'title')->first();
        $this->assertEquals('작업명', $titleFieldInfo['name']);
        $this->assertTrue($titleFieldInfo['is_primary']);
        
        // 레코드 데이터 확인
        $this->assertArrayHasKey('records', $data);
        $this->assertCount(2, $data['records']);
        
        $records = $data['records'];
        $firstRecord = $records->where('record_number', 'TASK-001')->first();
        
        $this->assertEquals('TASK-001', $firstRecord['record_number']);
        $this->assertArrayHasKey('fields', $firstRecord);
        
        // 필드별 값 확인
        $recordFields = $firstRecord['fields'];
        
        // 텍스트 필드
        $this->assertEquals('로그인 기능 개발', $recordFields['title']['value']);
        $this->assertEquals('로그인 기능 개발', $recordFields['title']['display_value']);
        
        // 선택 필드
        $this->assertEquals('진행중', $recordFields['status']['value']);
        $this->assertEquals('진행중', $recordFields['status']['display_value']);
        
        // 평점 필드
        $this->assertEquals(5, $recordFields['priority']['value']);
        $this->assertEquals('⭐⭐⭐⭐⭐', $recordFields['priority']['display_value']);
        
        // 체크박스 필드
        $this->assertFalse($recordFields['completed']['value']);
        $this->assertEquals('', $recordFields['completed']['display_value']);
        
        // 두 번째 레코드의 체크박스 확인
        $secondRecord = $records->where('record_number', 'TASK-002')->first();
        $this->assertTrue($secondRecord['fields']['completed']['value']);
        $this->assertEquals('✓', $secondRecord['fields']['completed']['display_value']);
    }
}