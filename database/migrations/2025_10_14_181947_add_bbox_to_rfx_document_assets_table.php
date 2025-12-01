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
        Schema::table('rfx_document_assets', function (Blueprint $table) {
            $table->json('bbox')->nullable()->after('confidence')->comment('OCR bounding box coordinates [[x1,y1], [x2,y2], [x3,y3], [x4,y4]]');
            $table->integer('bbox_x')->nullable()->after('bbox')->comment('Bounding box top-left X coordinate');
            $table->integer('bbox_y')->nullable()->after('bbox_x')->comment('Bounding box top-left Y coordinate');
            $table->integer('bbox_width')->nullable()->after('bbox_y')->comment('Bounding box width');
            $table->integer('bbox_height')->nullable()->after('bbox_width')->comment('Bounding box height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfx_document_assets', function (Blueprint $table) {
            $table->dropColumn(['bbox', 'bbox_x', 'bbox_y', 'bbox_width', 'bbox_height']);
        });
    }
};
