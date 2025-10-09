<?php

namespace Tests\Feature\Pms\ProjectList\Search;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Plobin\Project;
use Livewire\Livewire;
use App\Livewire\Pms\ProjectList\Livewire as ProjectListLivewire;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_프로젝트_검색이_정상적으로_작동한다(): void
    {
        // Given: 여러 프로젝트가 있을 때
        Project::factory()->create([
            'name' => '웹사이트 프로젝트',
            'description' => '회사 웹사이트 개발'
        ]);
        
        Project::factory()->create([
            'name' => '모바일 앱',
            'description' => '모바일 애플리케이션 개발'
        ]);

        // When: '웹사이트'로 검색하면
        Livewire::test(ProjectListLivewire::class)
            ->set('search', '웹사이트')
            ->assertSee('웹사이트 프로젝트')
            ->assertDontSee('모바일 앱');
    }
}