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
        Schema::create('sandbox_pms_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_id')->comment('베이스 외래키');
            $table->string('name')->comment('테이블명');
            $table->string('slug')->comment('시스템 식별자');
            $table->text('description')->nullable()->comment('설명');
            $table->string('icon')->nullable()->comment('아이콘');
            $table->string('color')->default('#6B7280')->comment('색상');
            $table->unsignedBigInteger('primary_field_id')->nullable()->comment('기본 표시 필드');
            $table->integer('sort_order')->default(0)->comment('정렬 순서');
            $table->boolean('is_active')->default(true)->comment('활성 여부');
            $table->timestamps();
            
            $table->foreign('base_id')->references('id')->on('sandbox_pms_bases')->onDelete('cascade');
            $table->unique(['base_id', 'slug']);
            $table->index(['base_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_pms_tables');
    }
};
