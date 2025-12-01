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
        Schema::create('plobin_page_summaries', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('document_summary_id', 26); // FK
            $table->integer('page_number');
            $table->integer('block_count');
            $table->text('ai_summary');
            $table->timestamps();

            $table->foreign('document_summary_id')
                  ->references('id')
                  ->on('plobin_document_summaries')
                  ->onDelete('cascade');

            $table->index(['document_summary_id', 'page_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_page_summaries');
    }
};
