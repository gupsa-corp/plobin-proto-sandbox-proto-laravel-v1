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
        Schema::create('plobin_gantt_non_working_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name', 100)->nullable();
            $table->boolean('recurring')->default(false);
            $table->timestamps();

            // 인덱스
            $table->index('date');
            $table->index('recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_gantt_non_working_days');
    }
};
