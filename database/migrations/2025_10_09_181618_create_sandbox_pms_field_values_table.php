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
        Schema::create('sandbox_pms_field_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id')->comment('레코드 외래키');
            $table->unsignedBigInteger('field_id')->comment('필드 외래키');
            $table->text('value_text')->nullable()->comment('텍스트 값');
            $table->decimal('value_number', 20, 8)->nullable()->comment('숫자 값');
            $table->datetime('value_date')->nullable()->comment('날짜 값');
            $table->boolean('value_boolean')->nullable()->comment('불린 값');
            $table->json('value_json')->nullable()->comment('JSON 값');
            $table->timestamps();
            
            $table->foreign('record_id')->references('id')->on('sandbox_pms_records')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('sandbox_pms_fields')->onDelete('cascade');
            $table->unique(['record_id', 'field_id']);
            $table->index(['field_id']);
            $table->index(['value_text']);
            $table->index(['value_number']);
            $table->index(['value_date']);
            $table->index(['value_boolean']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_pms_field_values');
    }
};
