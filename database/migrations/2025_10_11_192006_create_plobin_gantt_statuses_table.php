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
        Schema::create('plobin_gantt_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->boolean('is_closed')->default(false);
            $table->string('color', 7)->default('#3498db');
            $table->integer('position')->default(0);
            $table->timestamps();

            // 인덱스
            $table->unique('name');
            $table->index('position');
            $table->index('is_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_gantt_statuses');
    }
};
