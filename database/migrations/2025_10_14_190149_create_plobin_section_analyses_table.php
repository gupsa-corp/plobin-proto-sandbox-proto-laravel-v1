<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plobin_section_analyses', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('page_summary_id', 26); // FK
            $table->string('block_id', 100); // 블록 ID
            $table->integer('block_index'); // 블록 순서
            $table->string('section_title', 255); // 섹션 제목
            $table->string('asset_type', 50); // 예: introduction, analysis, recommendation
            $table->string('asset_type_name', 100); // 예: 서론/개요, 분석, 제안/권고
            $table->string('asset_type_icon', 10); // 예: 🎯, 📊, 💡
            $table->text('original_content'); // 원문
            $table->text('ai_summary'); // AI 요약
            $table->text('helpful_content')->nullable(); // 도움되는 내용
            $table->string('current_version_number', 50); // 현재 버전 (YmdHisu)
            $table->timestamps();

            $table->foreign('page_summary_id')
                  ->references('id')
                  ->on('plobin_page_summaries')
                  ->onDelete('cascade');

            $table->index(['page_summary_id', 'block_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_section_analyses');
    }
};
