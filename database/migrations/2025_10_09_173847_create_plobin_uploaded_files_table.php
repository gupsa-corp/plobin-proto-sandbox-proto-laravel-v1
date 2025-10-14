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
        Schema::create('plobin_uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->string('status')->default('uploaded'); // uploaded, analyzing, completed, error
            $table->foreignId('uploaded_by')->constrained('plobin_users');
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('uploaded_by');
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_uploaded_files');
    }
};
