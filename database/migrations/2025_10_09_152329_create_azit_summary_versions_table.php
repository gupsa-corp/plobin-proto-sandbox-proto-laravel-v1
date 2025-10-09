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
        Schema::create('sandbox_summary_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('summary_id');
            $table->integer('version_number');
            $table->longText('ai_summary');
            $table->longText('helpful_content')->nullable();
            $table->enum('edit_type', ['ai_generated', 'user_edit', 'auto_improved'])->default('user_edit');
            $table->text('edit_notes')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamp('created_at');

            $table->foreign('summary_id')->references('id')->on('sandbox_asset_summaries')->onDelete('cascade');
            $table->index(['summary_id', 'version_number']);
            $table->index(['summary_id', 'is_current']);
            $table->index(['edit_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_summary_versions');
    }
};
