<?php

namespace Tests\Unit\Models\Sandbox\SandboxFieldValue\Casting;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxFieldValue;
use App\Models\Plobin\SandboxBase;
use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxField;
use App\Models\Plobin\SandboxRecord;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_캐스팅이_올바르게_작동한다(): void
    {
        // Given: 실제 베이스, 테이블, 필드, 레코드 생성
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

        $field = SandboxField::create([
            'table_id' => $table->id,
            'name' => 'Test Field',
            'slug' => 'test_field',
            'field_type' => 'number',
            'sort_order' => 1
        ]);

        $record = SandboxRecord::create([
            'table_id' => $table->id,
            'record_number' => 'TEST-001',
            'created_by' => 1
        ]);

        // Given: 다양한 값을 가진 FieldValue
        $fieldValue = SandboxFieldValue::create([
            'record_id' => $record->id,
            'field_id' => $field->id,
            'value_number' => '42.123456789',
            'value_date' => '2024-01-01 10:00:00',
            'value_boolean' => '1',
            'value_json' => ['item1', 'item2']
        ]);

        // When & Then: 캐스팅이 올바르게 작동하는지 확인
        $this->assertEquals(42.12345679, $fieldValue->value_number); // decimal:8 캐스팅
        $this->assertInstanceOf(\Carbon\Carbon::class, $fieldValue->value_date);
        $this->assertTrue($fieldValue->value_boolean);
        $this->assertEquals(['item1', 'item2'], $fieldValue->value_json);
    }
}