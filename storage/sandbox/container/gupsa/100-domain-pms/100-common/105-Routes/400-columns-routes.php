<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require_once __DIR__ . '/../104-Repository/400-query-columns.php';

// 컬럼 목록 조회
Route::get('columns', function (Request $request) {
    try {
        $repository = new ColumnsRepository();
        $type = $request->input('type', 'all');
        
        $columns = $repository->getList($type);
        
        return response()->json(['success' => true, 'data' => $columns]);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 새 컬럼 추가
Route::post('columns', function (Request $request) {
    try {
        $repository = new ColumnsRepository();
        $columnId = $repository->create($request->all());
        
        // 생성된 컬럼 정보 반환
        $newColumn = $repository->findById($columnId);
        
        return response()->json(['success' => true, 'data' => $newColumn], 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 컬럼 수정
Route::put('columns/{id}', function (Request $request, $id) {
    try {
        $repository = new ColumnsRepository();
        
        $success = $repository->update($id, $request->all());
        
        if (!$success) {
            return response()->json(['success' => false, 'message' => 'Column not found'], 404);
        }
        
        return response()->json(['success' => true, 'message' => 'Column updated successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 컬럼 삭제
Route::delete('columns/{id}', function (Request $request, $id) {
    try {
        $repository = new ColumnsRepository();
        $repository->delete($id);
        
        return response()->json(['success' => true, 'message' => 'Column deleted successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});