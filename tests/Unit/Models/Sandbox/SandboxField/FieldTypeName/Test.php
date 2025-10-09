<?php

namespace Tests\Unit\Models\Sandbox\SandboxField\FieldTypeName;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxField;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_필드_타입명_속성이_올바르게_반환된다(): void
    {
        // Given: 다양한 필드 타입으로 필드 생성
        $textField = new SandboxField(['field_type' => 'singleLineText']);
        $emailField = new SandboxField(['field_type' => 'email']);
        $selectField = new SandboxField(['field_type' => 'singleSelect']);
        $unknownField = new SandboxField(['field_type' => 'unknown_type']);

        // When & Then: 필드 타입명이 올바르게 반환되는지 확인
        $this->assertEquals('한줄 텍스트', $textField->field_type_name);
        $this->assertEquals('이메일', $emailField->field_type_name);
        $this->assertEquals('단일 선택', $selectField->field_type_name);
        $this->assertEquals('unknown_type', $unknownField->field_type_name);
    }
}