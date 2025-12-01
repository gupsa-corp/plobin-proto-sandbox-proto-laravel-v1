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
        Schema::create('sandbox_pms_bases', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('베이스명');
            $table->string('slug')->unique()->comment('URL용 식별자');
            $table->text('description')->nullable()->comment('설명');
            $table->string('icon')->nullable()->comment('아이콘');
            $table->string('color')->default('#3B82F6')->comment('테마 색상');
            $table->boolean('is_active')->default(true)->comment('활성 여부');
            $table->unsignedBigInteger('created_by')->comment('생성자');
            $table->timestamps();
            
            $table->index(['is_active', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_pms_bases');
    }
};
