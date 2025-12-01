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
        Schema::create('sandbox_uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->bigInteger('file_size')->default(0);
            $table->string('mime_type')->default('application/octet-stream');
            $table->boolean('is_analysis_requested')->default(false);
            $table->boolean('is_analysis_completed')->default(false);
            $table->enum('analysis_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamp('analysis_requested_at')->nullable();
            $table->timestamp('analysis_completed_at')->nullable();
            $table->timestamps();

            $table->index(['analysis_status']);
            $table->index(['is_analysis_completed']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_uploaded_files');
    }
};
