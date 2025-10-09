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
        Schema::create('sandbox_asset_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->longText('ai_summary');
            $table->longText('helpful_content')->nullable();
            $table->enum('analysis_status', ['processing', 'completed', 'failed'])->default('completed');
            $table->json('analysis_metadata')->nullable();
            $table->integer('version_count')->default(1);
            $table->integer('current_version')->default(1);
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('sandbox_document_assets')->onDelete('cascade');
            $table->index(['asset_id']);
            $table->index(['analysis_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_asset_summaries');
    }
};
