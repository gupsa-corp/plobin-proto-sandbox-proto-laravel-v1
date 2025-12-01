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
        Schema::create('plobin_gantt_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('position')->default(0);
            $table->string('color', 7)->default('#3498db');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // 인덱스
            $table->unique('name');
            $table->index('position');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_gantt_priorities');
    }
};
