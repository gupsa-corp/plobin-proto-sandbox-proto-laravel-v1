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
        Schema::create('rfx_document_assets', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('analysis_request_id', 36);
            $table->string('asset_id', 50);
            $table->string('section_title', 255);
            $table->string('asset_type', 50);
            $table->string('asset_type_name', 100);
            $table->string('asset_type_icon', 10);
            $table->text('content');
            $table->integer('page_number')->default(1);
            $table->decimal('confidence', 5, 4)->default(0);
            $table->integer('display_order')->default(0);
            $table->string('status', 20)->default('pending');
            $table->string('status_icon', 10)->default('⏳');
            $table->timestamps();

            $table->index('analysis_request_id', 'idx_analysis_request');
            $table->index('display_order', 'idx_display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfx_document_assets');
    }
};
