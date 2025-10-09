<?php

namespace App\Services\Sandbox\GetBase;

use App\Models\Plobin\SandboxBase;

/**
 * 에어테이블 스타일 베이스 상세 조회 서비스
 */
class Service
{
    public function execute(int $baseId): array
    {
        $base = SandboxBase::with([
            'tables.fields' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            },
            'tables.views'
        ])->find($baseId);

        if (!$base) {
            return ['success' => false, 'message' => 'Base not found'];
        }

        return [
            'success' => true,
            'data' => [
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
                        'fields' => $table->fields->map(function($field) {
                            return [
                                'id' => $field->id,
                                'name' => $field->name,
                                'slug' => $field->slug,
                                'field_type' => $field->field_type,
                                'field_type_name' => $field->field_type_name,
                                'field_config' => $field->field_config,
                                'is_required' => $field->is_required,
                                'is_primary' => $field->is_primary
                            ];
                        }),
                        'views' => $table->views->map(function($view) {
                            return [
                                'id' => $view->id,
                                'name' => $view->name,
                                'view_type' => $view->view_type,
                                'view_type_name' => $view->view_type_name,
                                'is_default' => $view->is_default
                            ];
                        })
                    ];
                })
            ]
        ];
    }
}