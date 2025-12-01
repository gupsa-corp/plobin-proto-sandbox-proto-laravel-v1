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
        Schema::create('sandbox_pms_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id')->comment('테이블 외래키');
            $table->string('name')->comment('필드명');
            $table->string('slug')->comment('시스템 식별자');
            $table->string('field_type')->comment('필드 타입');
            $table->json('field_config')->nullable()->comment('타입별 설정');
            $table->boolean('is_required')->default(false)->comment('필수 여부');
            $table->boolean('is_primary')->default(false)->comment('기본 표시 필드 여부');
            $table->integer('sort_order')->default(0)->comment('표시 순서');
            $table->boolean('is_active')->default(true)->comment('활성 여부');
            $table->timestamps();
            
            $table->foreign('table_id')->references('id')->on('sandbox_pms_tables')->onDelete('cascade');
            $table->unique(['table_id', 'slug']);
            $table->index(['table_id', 'is_active', 'sort_order']);
            $table->index(['field_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_pms_fields');
    }
};
