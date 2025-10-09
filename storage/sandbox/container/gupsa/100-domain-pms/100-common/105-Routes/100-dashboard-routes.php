<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require_once __DIR__ . '/../104-Repository/100-query-dashboard.php';

// 대시보드 통계 조회
Route::get('dashboard/stats', function (Request $request) {
    try {
        $repository = new DashboardRepository();
        $stats = $repository->getStats();
        
        return response()->json($stats);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});