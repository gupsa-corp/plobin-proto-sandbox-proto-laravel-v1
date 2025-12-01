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
        Schema::create('sandbox_document_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id');
            $table->enum('asset_type', [
                'introduction', 'methodology', 'findings', 'analysis', 
                'conclusion', 'recommendation', 'technical_spec', 'data_analysis',
                'case_study', 'appendix', 'reference', 'summary', 'other'
            ]);
            $table->string('section_title');
            $table->integer('order_index')->default(0);
            $table->longText('content');
            $table->json('metadata')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            $table->foreign('file_id')->references('id')->on('sandbox_uploaded_files')->onDelete('cascade');
            $table->index(['file_id', 'order_index']);
            $table->index(['asset_type']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_document_assets');
    }
};
