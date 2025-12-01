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
        Schema::create('plobin_analysis_request_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_request_id')->constrained('plobin_analysis_requests')->onDelete('cascade');
            $table->foreignId('uploaded_file_id')->constrained('plobin_uploaded_files')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['analysis_request_id', 'uploaded_file_id'], 'request_file_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_analysis_request_files');
    }
};
