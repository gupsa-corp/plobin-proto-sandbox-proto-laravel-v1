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
        Schema::create('sandbox_pms_field_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_field_id')->comment('소스 필드');
            $table->unsignedBigInteger('target_table_id')->comment('대상 테이블');
            $table->unsignedBigInteger('target_field_id')->nullable()->comment('대상 필드 (역방향 관계)');
            $table->enum('link_type', ['one_to_one', 'one_to_many', 'many_to_many'])->default('one_to_many')->comment('관계 타입');
            $table->timestamps();
            
            $table->foreign('source_field_id')->references('id')->on('sandbox_pms_fields')->onDelete('cascade');
            $table->foreign('target_table_id')->references('id')->on('sandbox_pms_tables')->onDelete('cascade');
            $table->foreign('target_field_id')->references('id')->on('sandbox_pms_fields')->onDelete('set null');
            $table->unique(['source_field_id']);
            $table->index(['target_table_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_pms_field_links');
    }
};
