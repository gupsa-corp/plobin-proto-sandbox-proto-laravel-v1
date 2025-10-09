<?php

namespace Tests\Unit\Models\Sandbox\SandboxField\FieldCasting;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxField;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_필드_캐스팅이_올바르게_작동한다(): void
    {
        // Given: 필드 설정이 있는 필드 생성
        $field = new SandboxField([
            'field_config' => ['options' => [['name' => 'test', 'color' => '#FF0000']]],
            'is_required' => '1',
            'is_primary' => '0',
            'is_active' => 'true'
        ]);

        // When & Then: 캐스팅이 올바르게 작동하는지 확인
        $this->assertIsArray($field->field_config);
        $this->assertArrayHasKey('options', $field->field_config);
        $this->assertTrue($field->is_required);
        $this->assertFalse($field->is_primary);
        $this->assertTrue($field->is_active);
    }
}