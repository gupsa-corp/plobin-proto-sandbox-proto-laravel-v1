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
        Schema::create('rfx_ocr_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('upload_id');
            $table->longText('ocr_result');
            $table->timestamps();

            $table->foreign('upload_id')->references('upload_id')->on('rfx_uploads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfx_ocr_results');
    }
};
