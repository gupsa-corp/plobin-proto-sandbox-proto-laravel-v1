<?php

namespace Tests\Feature\Rfx\DocumentAsset\RegenerateSummary\Success;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Rfx\DocumentAsset\RegenerateSummary\Service;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_기존_요약이_성공적으로_재분석된다(): void
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
            'status' => 'completed',
            'status_icon' => '✅',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // And: 기존 Summary가 있고
        $summaryId = (string) Str::ulid();
        $firstVersionTimestamp = now()->subHours(1)->format('YmdHis');
        DB::table('rfx_asset_summaries')->insert([
            'id' => $summaryId,
            'asset_id' => $assetId,
            'ai_summary' => '결제 정보 요약',
            'helpful_content' => '도움되는 내용',
            'confidence' => 0.85,
            'current_version_timestamp' => $firstVersionTimestamp,
            'created_at' => now()->subHours(1),
            'updated_at' => now()->subHours(1),
        ]);

        // And: 첫 번째 버전이 있고
        DB::table('rfx_summary_versions')->insert([
            'id' => (string) Str::ulid(),
            'summary_id' => $summaryId,
            'version_timestamp' => $firstVersionTimestamp,
            'ai_summary' => '결제 정보 요약',
            'helpful_content' => '도움되는 내용',
            'edited_by' => 'ai',
            'created_at' => now()->subHours(1),
        ]);

        // When: 재분석 Service를 실행하면
        $service = new Service();
        $result = $service->execute($assetId);

        // Then: 성공 응답을 받는다
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('ai_summary', $result['data']);
        $this->assertArrayHasKey('helpful_content', $result['data']);
        $this->assertArrayHasKey('version_timestamp', $result['data']);

        // And: Summary가 업데이트된다
        $updatedSummary = DB::table('rfx_asset_summaries')
            ->where('id', $summaryId)
            ->first();

        $this->assertNotEquals($firstVersionTimestamp, $updatedSummary->current_version_timestamp);
        $this->assertStringContainsString('[재분석]', $updatedSummary->ai_summary);

        // And: 새 버전이 생성된다
        $versions = DB::table('rfx_summary_versions')
            ->where('summary_id', $summaryId)
            ->orderBy('created_at')
            ->get();

        $this->assertCount(2, $versions);
        $this->assertEquals('ai', $versions[1]->edited_by);
        $this->assertStringContainsString('[재분석]', $versions[1]->ai_summary);
    }
}
