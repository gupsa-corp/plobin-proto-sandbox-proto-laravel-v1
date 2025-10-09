<?php

namespace Tests\Feature\Pms\ProjectList\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Plobin\Project;
use Livewire\Livewire;
use App\Livewire\Pms\ProjectList\Livewire as ProjectListLivewire;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_프로젝트_목록_페이지가_정상적으로_로드된다(): void
    {
        // Given: 프로젝트 데이터가 있을 때
        Project::factory()->create([
            'name' => '테스트 프로젝트',
            'description' => '테스트용 프로젝트입니다',
            'status' => 'planning',
            'priority' => 'high'
        ]);

        // When: 프로젝트 목록 페이지에 접근하면
        $response = $this->get('/pms/projects');

        // Then: 페이지가 정상적으로 로드되고 프로젝트가 표시된다
        $response->assertStatus(200);
        $response->assertSee('테스트 프로젝트');
        $response->assertSee('테스트용 프로젝트입니다');
    }
}