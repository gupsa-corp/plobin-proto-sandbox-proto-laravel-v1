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
        Schema::create('rfx_analysis_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('ocr_request_id', 100);
            $table->unsignedBigInteger('file_id')->nullable();
            $table->string('version_timestamp', 14);
            $table->enum('version_type', ['original', 'reanalysis'])->default('reanalysis');

            // 스냅샷 데이터
            $table->mediumText('snapshot_data');
            $table->text('summary')->nullable();
            $table->json('keywords')->nullable();
            $table->json('categories')->nullable();
            $table->json('extracted_data')->nullable();

            // 메타데이터
            $table->boolean('is_latest')->default(false);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->text('snapshot_reason')->nullable();
            $table->timestamps();

            // 외래키
            $table->foreign('file_id')
                ->references('id')
                ->on('rfx_ai_analysis_requests')
                ->onDelete('cascade');

            // 인덱스
            $table->index(['ocr_request_id', 'version_timestamp'], 'idx_request_version');
            $table->index('is_latest', 'idx_latest');
            $table->index('file_id', 'idx_file');
            $table->index('created_at', 'idx_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfx_analysis_snapshots');
    }
};
