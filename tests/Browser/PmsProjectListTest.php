<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PmsProjectListTest extends DuskTestCase
{
    public function test_프로젝트_목록_페이지_로드_및_기본_기능(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/projects')
                    ->assertSee('프로젝트 목록')
                    ->assertSee('새 프로젝트 추가')
                    ->assertPresent('[wire:click="openCreateModal"]')
                    ->assertPresent('[wire:click="switchToTableView"]');
        });
    }

    public function test_새_프로젝트_생성_모달_열기(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/projects')
                    ->click('[wire:click="openCreateModal"]')
                    ->waitFor('.fixed.inset-0', 3) // 모달이 열릴 때까지 최대 3초 대기
                    ->assertSee('새 프로젝트 추가')
                    ->assertPresent('input[wire:model="projectForm.name"]')
                    ->assertPresent('textarea[wire:model="projectForm.description"]');
        });
    }

    public function test_테이블_뷰_전환(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pms/projects')
                    ->click('[wire:click="switchToTableView"]')
                    ->waitForLocation('/pms/table-view', 3)
                    ->assertSee('프로젝트 테이블 뷰');
        });
    }
}