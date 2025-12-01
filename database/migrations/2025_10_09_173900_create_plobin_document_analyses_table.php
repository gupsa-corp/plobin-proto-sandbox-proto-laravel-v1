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
        Schema::create('plobin_document_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('plobin_uploaded_files');
            $table->foreignId('request_id')->nullable()->constrained('plobin_analysis_requests');
            $table->string('status')->default('pending'); // pending, analyzing, completed, error
            $table->text('summary')->nullable();
            $table->json('keywords')->nullable();
            $table->json('categories')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->json('extracted_data')->nullable();
            $table->json('recommendations')->nullable();
            $table->string('document_type')->nullable();
            $table->unsignedInteger('keyword_count')->nullable();
            $table->unsignedInteger('page_count')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
            
            $table->index('file_id');
            $table->index('request_id');
            $table->index(['status', 'analyzed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_document_analyses');
    }
};
