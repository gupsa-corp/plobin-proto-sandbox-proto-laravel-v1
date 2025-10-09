<?php

namespace Tests\Feature\Pms\ProjectList\ViewProject;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Pms\ProjectList\Livewire as ProjectListLivewire;
use App\Models\Plobin\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_프로젝트_상세보기가_작동한다(): void
    {
        // Given: 프로젝트가 존재할 때
        $project = Project::factory()->create([
            'name' => '웹사이트 리뉴얼 프로젝트',
            'status' => 'in_progress',
            'progress' => 75
        ]);

        // When: 프로젝트 상세보기를 호출하면
        Livewire::test(ProjectListLivewire::class)
            ->call('viewProject', $project->id)
            ->assertSet('successMessage', "프로젝트 '웹사이트 리뉴얼 프로젝트' 상세 정보: 진행률 75%, 상태: 진행중");
    }

    public function test_존재하지_않는_프로젝트_조회_시_메시지가_없다(): void
    {
        Livewire::test(ProjectListLivewire::class)
            ->call('viewProject', 999)
            ->assertSessionMissing('message');
    }

    public function test_테이블_뷰_전환이_작동한다(): void
    {
        $response = Livewire::test(ProjectListLivewire::class)
            ->call('switchToTableView');
            
        // 리다이렉트가 일어나는지 확인
        $response->assertRedirect(route('pms.table-view'));
    }
}