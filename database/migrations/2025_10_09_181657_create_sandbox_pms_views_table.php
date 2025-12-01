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
        Schema::create('sandbox_pms_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id')->comment('테이블 외래키');
            $table->string('name')->comment('뷰명');
            $table->enum('view_type', ['grid', 'kanban', 'calendar', 'gallery', 'form'])->default('grid')->comment('뷰 타입');
            $table->json('filter_config')->nullable()->comment('필터 설정');
            $table->json('sort_config')->nullable()->comment('정렬 설정');
            $table->json('field_config')->nullable()->comment('필드 표시 설정');
            $table->json('group_config')->nullable()->comment('그룹화 설정');
            $table->boolean('is_default')->default(false)->comment('기본 뷰 여부');
            $table->unsignedBigInteger('created_by')->comment('생성자');
            $table->timestamps();
            
            $table->foreign('table_id')->references('id')->on('sandbox_pms_tables')->onDelete('cascade');
            $table->index(['table_id', 'is_default']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_pms_views');
    }
};
