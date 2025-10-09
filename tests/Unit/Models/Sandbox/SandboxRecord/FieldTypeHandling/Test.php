<?php

namespace Tests\Unit\Models\Sandbox\SandboxRecord\FieldTypeHandling;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxBase;
use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxField;
use App\Models\Plobin\SandboxRecord;
use App\Models\Plobin\SandboxFieldValue;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_다양한_필드_타입의_값_조회가_올바르게_작동한다(): void
    {
        // Given: 다양한 필드 타입을 가진 테이블과 레코드
        $base = SandboxBase::create([
            'name' => 'Test Base',
            'slug' => 'test-base',
            'created_by' => 1
        ]);

        $table = SandboxTable::create([
            'base_id' => $base->id,
            'name' => 'Test Table',
            'slug' => 'test-table',
            'sort_order' => 1
        ]);

        $checkboxField = SandboxField::create([
            'table_id' => $table->id,
            'name' => 'Completed',
            'slug' => 'completed',
            'field_type' => 'checkbox',
            'sort_order' => 1
        ]);

        $dateField = SandboxField::create([
            'table_id' => $table->id,
            'name' => 'Due Date',
            'slug' => 'due_date',
            'field_type' => 'date',
            'sort_order' => 2
        ]);

        $arrayField = SandboxField::create([
            'table_id' => $table->id,
            'name' => 'Tags',
            'slug' => 'tags',
            'field_type' => 'multipleSelect',
            'sort_order' => 3
        ]);

        $record = SandboxRecord::create([
            'table_id' => $table->id,
            'record_number' => 'TEST-003',
            'created_by' => 1
        ]);

        // 다양한 타입의 필드 값 생성
        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $checkboxField->id,
            'value_boolean' => true
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $dateField->id,
            'value_date' => '2024-12-31 00:00:00'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $arrayField->id,
            'value_json' => ['tag1', 'tag2', 'tag3']
        ]);

        // 관계 로드
        $record->load('sandboxTable.fields', 'fieldValues');
        
        // When & Then: 각 타입별 값 조회 확인
        $this->assertTrue($record->getFieldValueBySlug('completed'));
        $this->assertInstanceOf(\Carbon\Carbon::class, $record->getFieldValueBySlug('due_date'));
        $this->assertEquals(['tag1', 'tag2', 'tag3'], $record->getFieldValueBySlug('tags'));
    }
}