<?php

namespace Tests\Unit\Models\Sandbox\SandboxFieldValue\GetValue;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxFieldValue;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_getValue_메서드가_적절한_값을_반환한다(): void
    {
        // Given: 다양한 타입의 값을 가진 FieldValue 객체들
        $textValue = new SandboxFieldValue(['value_text' => 'Hello World']);
        $numberValue = new SandboxFieldValue(['value_number' => 42.5]);
        $dateValue = new SandboxFieldValue(['value_date' => '2024-01-01 10:00:00']);
        $booleanValue = new SandboxFieldValue(['value_boolean' => true]);
        $jsonValue = new SandboxFieldValue(['value_json' => ['key' => 'value']]);
        $nullValue = new SandboxFieldValue();

        // When & Then: 각 타입별로 올바른 값이 반환되는지 확인
        $this->assertEquals('Hello World', $textValue->getValue());
        $this->assertEquals(42.5, $numberValue->getValue());
        $this->assertInstanceOf(\DateTime::class, $dateValue->getValue());
        $this->assertTrue($booleanValue->getValue());
        $this->assertEquals(['key' => 'value'], $jsonValue->getValue());
        $this->assertNull($nullValue->getValue());
    }
}