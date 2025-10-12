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
        Schema::create('plobin_gantt_work_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('plobin_gantt_projects')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('plobin_gantt_work_packages')->onDelete('set null');
            $table->foreignId('type_id')->constrained('plobin_gantt_work_package_types')->onDelete('restrict');
            $table->foreignId('status_id')->constrained('plobin_gantt_statuses')->onDelete('restrict');
            $table->foreignId('priority_id')->constrained('plobin_gantt_priorities')->onDelete('restrict');

            $table->string('subject', 255);
            $table->text('description')->nullable();

            $table->date('start_date');
            $table->date('due_date');
            $table->integer('duration')->nullable();
            $table->boolean('schedule_manually')->default(false);

            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->tinyInteger('done_ratio')->default(0)->unsigned();

            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('responsible_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('author_id')->constrained('users')->onDelete('restrict');

            $table->integer('position')->default(0);
            $table->integer('lock_version')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // 인덱스
            $table->index(['project_id', 'start_date', 'due_date']);
            $table->index('parent_id');
            $table->index('assigned_to_id');
            $table->index('responsible_id');
            $table->index('status_id');
            $table->index('type_id');
            $table->index('position');
            $table->index(['project_id', 'status_id']);
            $table->index('schedule_manually');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plobin_gantt_work_packages');
    }
};
