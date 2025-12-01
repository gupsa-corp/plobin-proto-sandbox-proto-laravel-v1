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
        Schema::create('plobin_gantt_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_id')->constrained('plobin_gantt_work_packages')->onDelete('cascade');
            $table->foreignId('to_id')->constrained('plobin_gantt_work_packages')->onDelete('cascade');
            $table->enum('relation_type', ['precedes', 'follows', 'blocks', 'blocked_by', 'relates'])->default('relates');
            $table->integer('delay_days')->default(0);
            $table->timestamps();

            // 인덱스
            $table->index(['from_id', 'relation_type']);
            $table->index('to_id');
            $table->unique(['from_id', 'to_id', 'relation_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_gantt_relations');
    }
};
