<?php

namespace Tests\Unit\Models\Sandbox\SandboxRecord\GetFieldValue;

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

    public function test_getFieldValue_메서드가_올바른_값을_반환한다(): void
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

        $textField = SandboxField::create([
            'table_id' => $table->id,
            'name' => 'Text Field',
            'slug' => 'text_field',
            'field_type' => 'singleLineText',
            'sort_order' => 1
        ]);

        $numberField = SandboxField::create([
            'table_id' => $table->id,
            'name' => 'Number Field',
            'slug' => 'number_field',
            'field_type' => 'number',
            'sort_order' => 2
        ]);

        $record = SandboxRecord::create([
            'table_id' => $table->id,
            'record_number' => 'TEST-001',
            'created_by' => 1
        ]);

        // 필드 값 생성
        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $textField->id,
            'value_text' => 'Hello World'
        ]);

        SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $numberField->id,
            'value_number' => 42
        ]);

        // When & Then: 필드 ID로 값 조회
        $this->assertEquals('Hello World', $record->getFieldValue($textField->id));
        $this->assertEquals(42, $record->getFieldValue($numberField->id));
        
        // 존재하지 않는 필드 ID
        $this->assertNull($record->getFieldValue(999));
    }
}