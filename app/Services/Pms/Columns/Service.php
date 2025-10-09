<?php

namespace App\Services\Pms\Columns;

/**
 * PMS 도메인 컬럼 관리 서비스
 */
class Service
{
    /**
     * 컬럼 목록 조회
     */
    public function execute($filters = [])
    {
        try {
            // TODO: 실제 컬럼 목록 조회 로직 구현
            $columns = [
                [
                    'id' => 1,
                    'column_name' => 'title',
                    'column_label' => '제목',
                    'column_type' => 'text',
                    'is_required' => true,
                    'is_visible' => true,
                    'sort_order' => 1
                ],
                [
                    'id' => 2,
                    'column_name' => 'status',
                    'column_label' => '상태',
                    'column_type' => 'select',
                    'is_required' => true,
                    'is_visible' => true,
                    'sort_order' => 2
                ],
                [
                    'id' => 3,
                    'column_name' => 'priority',
                    'column_label' => '우선순위',
                    'column_type' => 'select',
                    'is_required' => false,
                    'is_visible' => true,
                    'sort_order' => 3
                ]
            ];

            return [
                'success' => true,
                'data' => $columns
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}