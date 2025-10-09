<?php

namespace Tests\Unit\Models\Sandbox\SandboxFieldValue\FieldTypeHandling;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxFieldValue;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_다양한_필드_타입에_대한_setValue_동작(): void
    {
        // Given: 필드 값 객체
        $fieldValue = new SandboxFieldValue();

        // When & Then: 이메일 필드 (텍스트로 저장)
        $fieldValue->setValue('test@example.com', 'email');
        $this->assertEquals('test@example.com', $fieldValue->value_text);

        // When & Then: 통화 필드 (숫자로 저장)
        $fieldValue->setValue(1000.50, 'currency');
        $this->assertEquals(1000.50, $fieldValue->value_number);

        // When & Then: 퍼센트 필드 (숫자로 저장)
        $fieldValue->setValue(75, 'percent');
        $this->assertEquals(75, $fieldValue->value_number);

        // When & Then: 평점 필드 (숫자로 저장)
        $fieldValue->setValue(5, 'rating');
        $this->assertEquals(5, $fieldValue->value_number);

        // When & Then: URL 필드 (텍스트로 저장)
        $fieldValue->setValue('https://example.com', 'url');
        $this->assertEquals('https://example.com', $fieldValue->value_text);

        // When & Then: 전화번호 필드 (텍스트로 저장)
        $fieldValue->setValue('010-1234-5678', 'phoneNumber');
        $this->assertEquals('010-1234-5678', $fieldValue->value_text);

        // When & Then: 날짜시간 필드 (날짜로 저장)
        $fieldValue->setValue('2024-01-01 15:30:00', 'dateTime');
        $this->assertEquals('2024-01-01 15:30:00', $fieldValue->value_date);

        // When & Then: 첨부파일 필드 (JSON으로 저장)
        $fieldValue->setValue([['name' => 'file.pdf', 'url' => '/files/file.pdf']], 'attachment');
        $this->assertEquals([['name' => 'file.pdf', 'url' => '/files/file.pdf']], $fieldValue->value_json);

        // When & Then: 알 수 없는 타입 (기본적으로 텍스트로 저장)
        $fieldValue->setValue('unknown value', 'unknown_type');
        $this->assertEquals('unknown value', $fieldValue->value_text);
    }
}