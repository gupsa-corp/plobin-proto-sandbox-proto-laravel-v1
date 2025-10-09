<?php

namespace Tests\Feature\Pms\ProjectList\CreateModal;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Pms\ProjectList\Livewire as ProjectListLivewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_새_프로젝트_생성_모달이_열린다(): void
    {
        Livewire::test(ProjectListLivewire::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->assertSee('새 프로젝트 추가');
    }

    public function test_새_프로젝트_생성_모달이_닫힌다(): void
    {
        Livewire::test(ProjectListLivewire::class)
            ->set('showCreateModal', true)
            ->call('closeCreateModal')
            ->assertSet('showCreateModal', false);
    }

    public function test_프로젝트_생성_유효성_검사가_작동한다(): void
    {
        Livewire::test(ProjectListLivewire::class)
            ->set('showCreateModal', true)
            ->call('createProject')
            ->assertHasErrors([
                'projectForm.name' => 'required',
                'projectForm.description' => 'required',
                'projectForm.start_date' => 'required',
                'projectForm.end_date' => 'required'
            ]);
    }

    public function test_프로젝트_생성_성공_시_메시지가_표시된다(): void
    {
        Livewire::test(ProjectListLivewire::class)
            ->set('showCreateModal', true)
            ->set('projectForm.name', '테스트 프로젝트')
            ->set('projectForm.description', '테스트 프로젝트 설명입니다. 최소 10자 이상 작성합니다.')
            ->set('projectForm.start_date', '2024-01-01')
            ->set('projectForm.end_date', '2024-12-31')
            ->call('createProject')
            ->assertSet('successMessage', '"테스트 프로젝트" 프로젝트가 성공적으로 생성되었습니다.')
            ->assertSet('showCreateModal', false);
    }
}