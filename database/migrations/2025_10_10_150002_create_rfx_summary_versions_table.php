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
        Schema::create('rfx_summary_versions', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('summary_id', 36);
            $table->string('version_timestamp', 20);
            $table->text('ai_summary');
            $table->text('helpful_content');
            $table->string('edited_by', 50);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('summary_id')
                ->references('id')
                ->on('rfx_asset_summaries')
                ->onDelete('cascade');

            $table->index(['summary_id', 'version_timestamp'], 'idx_summary_version');
            $table->index('version_timestamp', 'idx_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfx_summary_versions');
    }
};
