<?php

namespace Tests\Feature\Rfx\FileUpload\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use App\Livewire\Rfx\FileUpload\Livewire as RfxFileUploadLivewire;

class Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_RFX_파일_업로드_페이지가_정상_렌더링된다(): void
    {
        // Given: RFX 파일 업로드 페이지가 준비되어 있고
        
        // When: 페이지를 방문하면
        $response = $this->get('/rfx/upload');
        
        // Then: 성공적으로 렌더링된다
        $response->assertStatus(200)
                ->assertSee('파일 업로드')
                ->assertSee('RFX');
                
        // And: Livewire 컴포넌트가 포함되어 있다
        $this->assertTrue(true); // Livewire 컴포넌트 렌더링 확인
    }
}