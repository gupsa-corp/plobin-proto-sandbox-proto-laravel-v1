<?php

namespace App\Services\Rfx\UploadList;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(array $params): array
    {
        $query = DB::table('rfx_uploads')
            ->select([
                'upload_id',
                'status',
                'original_filename',
                'file_size',
                'file_type',
                'created_at',
                'updated_at'
            ])
            ->orderBy('created_at', 'desc');

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        $limit = min(max((int)$params['limit'], 1), 100);
        $uploads = $query->limit($limit)->get();

        return $uploads->toArray();
    }
}