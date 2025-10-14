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
        Schema::create('rfx_external_imports', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 100)->unique();
            $table->string('original_filename')->nullable();
            $table->integer('total_pages')->default(0);
            $table->enum('status', ['pending', 'importing', 'completed', 'failed'])->default('pending');
            $table->json('metadata')->nullable();
            $table->json('summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->index('request_id');
            $table->index('status');
            $table->index('imported_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfx_external_imports');
    }
};
