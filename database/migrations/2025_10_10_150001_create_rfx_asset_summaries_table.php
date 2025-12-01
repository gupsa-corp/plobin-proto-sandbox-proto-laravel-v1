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
        Schema::create('rfx_asset_summaries', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('asset_id', 36);
            $table->text('ai_summary');
            $table->text('helpful_content');
            $table->decimal('confidence', 5, 4)->default(0);
            $table->string('current_version_timestamp', 20);
            $table->timestamps();

            $table->foreign('asset_id')
                ->references('id')
                ->on('rfx_document_assets')
                ->onDelete('cascade');

            $table->index('asset_id', 'idx_asset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfx_asset_summaries');
    }
};
