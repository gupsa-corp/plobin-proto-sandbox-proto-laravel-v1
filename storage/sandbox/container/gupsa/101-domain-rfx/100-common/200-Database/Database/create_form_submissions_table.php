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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('form_name', 100); // 폼 이름
            $table->json('form_data'); // 제출된 폼 데이터 (JSON)
            $table->timestamp('submitted_at'); // 제출 시간
            $table->string('ip_address', 45)->nullable(); // IP 주소 (IPv6 지원)
            $table->text('user_agent')->nullable(); // User Agent
            $table->string('session_id', 100)->nullable(); // 세션 ID
            $table->unsignedBigInteger('user_id')->nullable(); // 제출한 사용자 ID (선택적)
            $table->timestamps(); // created_at, updated_at

            // 인덱스
            $table->index('form_name');
            $table->index('submitted_at');
            $table->index('user_id');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};