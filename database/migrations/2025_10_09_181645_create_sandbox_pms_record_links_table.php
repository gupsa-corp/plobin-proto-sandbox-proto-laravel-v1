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
        Schema::create('sandbox_pms_record_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_link_id')->comment('관계 정의 외래키');
            $table->unsignedBigInteger('source_record_id')->comment('소스 레코드');
            $table->unsignedBigInteger('target_record_id')->comment('대상 레코드');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('field_link_id')->references('id')->on('sandbox_pms_field_links')->onDelete('cascade');
            $table->foreign('source_record_id')->references('id')->on('sandbox_pms_records')->onDelete('cascade');
            $table->foreign('target_record_id')->references('id')->on('sandbox_pms_records')->onDelete('cascade');
            $table->unique(['field_link_id', 'source_record_id', 'target_record_id'], 'unique_record_link');
            $table->index(['source_record_id']);
            $table->index(['target_record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_pms_record_links');
    }
};
