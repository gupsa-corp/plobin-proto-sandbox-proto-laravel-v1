<?php

namespace Tests\Unit\Models\Sandbox\SandboxRecord\GetFieldValueBySlug;

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

    public function test_getFieldValueBySlug_메서드가_올바른_값을_반환한다(): void
    {
        // Given: 베이스, 테이블, 필드, 레코드 생성
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

        $emailField = SandboxField::create([
            'table_id' => $table->id,
            'name' => 'Email',
            'slug' => 'email',
            'field_type' => 'email',
            'sort_order' => 1
        ]);

        $statusField = SandboxField::create([
            'table_id' => $table->id,
            'name' => 'Status',
            'slug' => 'status',
            'field_type' => 'singleSelect',
            'sort_order' => 2
        ]);

        $record = SandboxRecord::create([
            'table_id' => $table->id,
            'record_number' => 'TEST-002',
            'created_by' => 1
        ]);

        // 필드 값 생성
        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $emailField->id,
            'value_text' => 'test@example.com'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $statusField->id,
            'value_json' => 'active'
        ]);

        // 관계 로드
        $record->load('sandboxTable.fields', 'fieldValues');
        
        // When & Then: 필드 슬러그로 값 조회
        $this->assertEquals('test@example.com', $record->getFieldValueBySlug('email'));
        $this->assertEquals('active', $record->getFieldValueBySlug('status'));
        
        // 존재하지 않는 필드 슬러그
        $this->assertNull($record->getFieldValueBySlug('nonexistent'));
    }
}