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
        Schema::create('plobin_pms_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('planning'); // planning, in_progress, review, completed
            $table->string('assignee')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->date('due_date')->nullable();
            $table->json('tags')->nullable();
            $table->integer('progress')->default(0); // 0-100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_pms_projects');
    }
};
