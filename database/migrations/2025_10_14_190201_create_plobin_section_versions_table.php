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
        Schema::create('plobin_section_versions', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('section_analysis_id', 26); // FK
            $table->string('version_number', 50); // YmdHisu 형식 (20250115143025123456)
            $table->string('version_display_name', 100); // 예: 2025-01-15 14:30:25 (AI 생성)
            $table->text('ai_summary'); // 해당 버전의 AI 요약
            $table->boolean('is_current')->default(false); // 현재 활성 버전 여부
            $table->string('created_by', 50); // AI 또는 사용자 ID
            $table->timestamp('created_at');

            $table->foreign('section_analysis_id')
                  ->references('id')
                  ->on('plobin_section_analyses')
                  ->onDelete('cascade');

            $table->index(['section_analysis_id', 'is_current']);
            $table->index('version_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_section_versions');
    }
};
