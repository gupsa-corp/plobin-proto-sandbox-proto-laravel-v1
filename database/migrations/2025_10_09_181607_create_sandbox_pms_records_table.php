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
        Schema::create('sandbox_pms_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id')->comment('테이블 외래키');
            $table->string('record_number')->comment('자동 생성 레코드 번호');
            $table->boolean('is_active')->default(true)->comment('삭제 여부');
            $table->unsignedBigInteger('created_by')->comment('생성자');
            $table->timestamps();
            
            $table->foreign('table_id')->references('id')->on('sandbox_pms_tables')->onDelete('cascade');
            $table->unique(['table_id', 'record_number']);
            $table->index(['table_id', 'is_active']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_pms_records');
    }
};
