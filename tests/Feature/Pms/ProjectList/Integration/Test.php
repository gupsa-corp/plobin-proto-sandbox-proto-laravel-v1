<?php

namespace Tests\Feature\Pms\ProjectList\Integration;

use Tests\TestCase;
use App\Models\Plobin\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_프로젝트_목록_페이지가_로드된다(): void
    {
        // Given: 테스트용 프로젝트 데이터 생성
        Project::factory()->create(['name' => '웹사이트 리뉴얼 프로젝트']);
        Project::factory()->create(['name' => '모바일 앱 개발']);
        Project::factory()->create(['name' => 'API 서버 구축']);

        $response = $this->get('/pms/projects');
        
        $response->assertStatus(200)
            ->assertSee('프로젝트 목록')
            ->assertSee('새 프로젝트 추가')
            ->assertSee('웹사이트 리뉴얼 프로젝트')
            ->assertSee('모바일 앱 개발')
            ->assertSee('API 서버 구축');
    }

    public function test_프로젝트_목록_페이지에_Livewire_스크립트가_포함된다(): void
    {
        $response = $this->get('/pms/projects');
        
        $response->assertStatus(200)
            ->assertSee('wire:click="openCreateModal"', false)
            ->assertSee('wire:click="switchToTableView"', false)
            ->assertSee('/livewire/livewire.js', false);
    }

    public function test_테이블_뷰_페이지가_로드된다(): void
    {
        $response = $this->get('/pms/table-view');
        
        $response->assertStatus(200)
            ->assertSee('프로젝트 테이블 뷰')
            ->assertSee('컬럼 설정')
            ->assertSee('새 프로젝트');
    }

    public function test_프로젝트_데이터가_올바르게_표시된다(): void
    {
        // Given: 특정 데이터를 가진 프로젝트 생성
        Project::factory()->create([
            'name' => '웹사이트 리뉴얼 프로젝트',
            'status' => 'in_progress',
            'priority' => 'high',
            'progress' => 75,
            'team' => ['김개발', '이디자인', '박기획']
        ]);

        $response = $this->get('/pms/projects');
        
        $response->assertStatus(200)
            ->assertSee('75%') // 웹사이트 리뉴얼 프로젝트 진행률
            ->assertSee('진행중')
            ->assertSee('높음')
            ->assertSee('김개발')
            ->assertSee('이디자인');
    }
}