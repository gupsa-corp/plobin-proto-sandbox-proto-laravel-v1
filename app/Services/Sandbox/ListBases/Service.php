<?php

namespace App\Services\Sandbox\ListBases;

use App\Models\Plobin\SandboxBase;

/**
 * 에어테이블 스타일 베이스 목록 조회 서비스
 */
class Service
{
    public function execute(): array
    {
        $bases = SandboxBase::where('is_active', true)
            ->with(['tables' => function($query) {
                $query->where('is_active', true)
                    ->orderBy('sort_order')
                    ->withCount('records');
            }])
            ->get();

        $formattedBases = $bases->map(function($base) {
            return [
                'id' => $base->id,
                'name' => $base->name,
                'slug' => $base->slug,
                'description' => $base->description,
                'icon' => $base->icon,
                'color' => $base->color,
                'tables' => $base->tables->map(function($table) {
                    return [
                        'id' => $table->id,
                        'name' => $table->name,
                        'slug' => $table->slug,
                        'icon' => $table->icon,
                        'color' => $table->color,
                        'record_count' => $table->records_count
                    ];
                })
            ];
        });

        return [
            'success' => true,
            'data' => $formattedBases
        ];
    }
}