<?php

namespace Tests\Feature\Rfx\DocumentAsset\GenerateAssetSummary\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Rfx\DocumentAsset\GenerateAssetSummary\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_요약이_없는_Asset에_대해_새로운_요약이_성공적으로_생성된다(): void
    {
        // Given: 분석 요청이 있고
        $analysisRequestId = DB::table('rfx_ai_analysis_requests')->insertGetId([
            'file_id' => 1,
            'file_name' => 'test.pdf',
            'file_type' => 'pdf',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // And: Document Asset이 있고
        $assetId = (string) Str::ulid();
        DB::table('rfx_document_assets')->insert([
            'id' => $assetId,
            'analysis_request_id' => $analysisRequestId,
            'asset_id' => 'asset_001',
            'section_title' => '결제 정보',
            'asset_type' => 'payment',
            'asset_type_name' => '결제',
            'asset_type_icon' => '💳',
            'content' => '결제 방법: 신용카드',
            'page_number' => 1,
            'confidence' => 0.9,
            'display_order' => 0,
            'status' => 'pending',
            'status_icon' => '⏳',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // When: 요약 생성 Service를 실행하면
        $service = new Service();
        $result = $service->execute($assetId);

        // Then: 성공 응답을 받는다
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('ai_summary', $result['data']);
        $this->assertArrayHasKey('helpful_content', $result['data']);
        $this->assertArrayHasKey('version_timestamp', $result['data']);

        // And: Summary가 생성된다
        $summary = DB::table('rfx_asset_summaries')
            ->where('asset_id', $assetId)
            ->first();

        $this->assertNotNull($summary);
        $this->assertNotEmpty($summary->ai_summary);
        $this->assertNotEmpty($summary->helpful_content);
        $this->assertNotEmpty($summary->current_version_timestamp);

        // And: 첫 버전이 생성된다
        $version = DB::table('rfx_summary_versions')
            ->where('summary_id', $summary->id)
            ->first();

        $this->assertNotNull($version);
        $this->assertEquals('ai', $version->edited_by);
        $this->assertEquals($summary->ai_summary, $version->ai_summary);
        $this->assertEquals($summary->helpful_content, $version->helpful_content);

        // And: Asset 상태가 업데이트된다
        $updatedAsset = DB::table('rfx_document_assets')
            ->where('id', $assetId)
            ->first();

        $this->assertEquals('completed', $updatedAsset->status);
        $this->assertEquals('✅', $updatedAsset->status_icon);
    }
}
