<?php

namespace Tests\Unit\Models\Sandbox\SandboxRecord\Relationships;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Plobin\SandboxBase;
use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxRecord;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_관계_메서드들이_올바르게_정의되어_있다(): void
    {
        // Given: 레코드 생성
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

        $record = SandboxRecord::create([
            'table_id' => $table->id,
            'record_number' => 'TEST-004',
            'created_by' => 1
        ]);

        // When & Then: 관계 메서드들이 올바른 타입을 반환하는지 확인
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $record->sandboxTable());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $record->fieldValues());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $record->sourceLinks());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $record->targetLinks());
    }
}