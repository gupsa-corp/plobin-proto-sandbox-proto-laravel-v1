<?php

namespace Tests\Unit\Models\Sandbox\SandboxFieldValue\SetValue;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxFieldValue;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_setValue_메서드가_필드_타입에_따라_올바르게_값을_설정한다(): void
    {
        // Given: 빈 FieldValue 객체
        $fieldValue = new SandboxFieldValue();

        // When & Then: 텍스트 필드 타입
        $fieldValue->setValue('Test Text', 'singleLineText');
        $this->assertEquals('Test Text', $fieldValue->value_text);
        $this->assertNull($fieldValue->value_number);
        $this->assertNull($fieldValue->value_date);
        $this->assertNull($fieldValue->value_boolean);
        $this->assertNull($fieldValue->value_json);

        // When & Then: 숫자 필드 타입
        $fieldValue->setValue(100, 'number');
        $this->assertNull($fieldValue->value_text);
        $this->assertEquals(100, $fieldValue->value_number);

        // When & Then: 날짜 필드 타입
        $fieldValue->setValue('2024-01-01', 'date');
        $this->assertStringContainsString('2024-01-01', $fieldValue->value_date);

        // When & Then: 불린 필드 타입
        $fieldValue->setValue(true, 'checkbox');
        $this->assertTrue($fieldValue->value_boolean);

        // When & Then: JSON 필드 타입
        $fieldValue->setValue(['option1', 'option2'], 'multipleSelect');
        $this->assertEquals(['option1', 'option2'], $fieldValue->value_json);
    }
}