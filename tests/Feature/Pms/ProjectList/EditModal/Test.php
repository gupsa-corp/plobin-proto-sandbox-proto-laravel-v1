<?php

namespace Tests\Feature\Pms\ProjectList\EditModal;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Pms\ProjectList\Livewire as ProjectListLivewire;
use App\Models\Plobin\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_프로젝트_편집_모달이_열린다(): void
    {
        // Given: 프로젝트가 존재할 때
        $project = Project::factory()->create([
            'name' => '웹사이트 리뉴얼 프로젝트',
            'status' => 'in_progress',
            'priority' => 'high'
        ]);
        
        // When: 편집 모달을 열면
        Livewire::test(ProjectListLivewire::class)
            ->call('editProject', $project->id)
            ->assertSet('showEditModal', true)
            ->assertSet('editingProjectId', $project->id);
    }

    public function test_프로젝트_편집_시_기존_데이터가_로드된다(): void
    {
        // Given: 프로젝트가 존재할 때
        $project = Project::factory()->create([
            'name' => '웹사이트 리뉴얼 프로젝트',
            'status' => 'in_progress',
            'priority' => 'high',
            'start_date' => '2024-01-01',
            'end_date' => '2024-06-30'
        ]);
        
        // When: 편집 모달을 열면
        Livewire::test(ProjectListLivewire::class)
            ->call('editProject', $project->id)
            ->assertSet('projectForm.name', '웹사이트 리뉴얼 프로젝트')
            ->assertSet('projectForm.status', 'in_progress')
            ->assertSet('projectForm.priority', 'high');
    }

    public function test_프로젝트_수정_성공_시_메시지가_표시된다(): void
    {
        // Given: 프로젝트가 존재할 때
        $project = Project::factory()->create([
            'name' => '원본 프로젝트명',
            'description' => '원본 설명입니다.',
            'start_date' => '2024-01-01',
            'end_date' => '2024-06-30'
        ]);
        
        // When: 프로젝트를 수정하면
        Livewire::test(ProjectListLivewire::class)
            ->call('editProject', $project->id)
            ->set('projectForm.name', '수정된 프로젝트명')
            ->set('projectForm.description', '수정된 프로젝트 설명입니다. 최소 10자 이상 작성합니다.')
            ->set('projectForm.start_date', '2024-01-01')
            ->set('projectForm.end_date', '2024-06-30')
            ->call('updateProject')
            ->assertSet('successMessage', "프로젝트 '수정된 프로젝트명'이 성공적으로 수정되었습니다.")
            ->assertSet('showEditModal', false);
    }
}