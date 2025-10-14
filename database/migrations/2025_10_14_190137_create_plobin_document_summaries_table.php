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
        Schema::create('plobin_document_summaries', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('document_id', 26); // OCR 문서 ID
            $table->integer('total_pages');
            $table->integer('total_blocks');
            $table->string('json_version', 50); // YmdHisu 형식 (20250115143025123456)
            $table->string('document_version', 50)->default('v1.0');
            $table->timestamps();

            $table->index('document_id');
            $table->index('json_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_document_summaries');
    }
};
