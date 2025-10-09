<?php

namespace Tests\Unit\Models\Sandbox\SandboxField\FieldTypeConstants;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxField;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_필드_타입_상수가_올바르게_정의되어_있다(): void
    {
        // Given & When: 필드 타입 상수 확인
        $fieldTypes = SandboxField::FIELD_TYPES;

        // Then: 주요 필드 타입들이 정의되어 있는지 확인
        $this->assertArrayHasKey('singleLineText', $fieldTypes);
        $this->assertArrayHasKey('multilineText', $fieldTypes);
        $this->assertArrayHasKey('number', $fieldTypes);
        $this->assertArrayHasKey('email', $fieldTypes);
        $this->assertArrayHasKey('singleSelect', $fieldTypes);
        $this->assertArrayHasKey('multipleSelect', $fieldTypes);
        $this->assertArrayHasKey('checkbox', $fieldTypes);
        $this->assertArrayHasKey('linkedRecord', $fieldTypes);
        $this->assertArrayHasKey('date', $fieldTypes);
        $this->assertArrayHasKey('currency', $fieldTypes);
        $this->assertArrayHasKey('percent', $fieldTypes);
        $this->assertArrayHasKey('rating', $fieldTypes);
        $this->assertArrayHasKey('url', $fieldTypes);
        $this->assertArrayHasKey('phoneNumber', $fieldTypes);
        $this->assertArrayHasKey('attachment', $fieldTypes);

        // 타입별 한글명 확인
        $this->assertEquals('한줄 텍스트', $fieldTypes['singleLineText']);
        $this->assertEquals('이메일', $fieldTypes['email']);
        $this->assertEquals('단일 선택', $fieldTypes['singleSelect']);
        $this->assertEquals('연결된 레코드', $fieldTypes['linkedRecord']);
    }
}