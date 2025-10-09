<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require_once __DIR__ . '/../104-Repository/600-query-permissions.php';

// 현재 사용자 권한 조회
Route::get('current-user-permissions', function (Request $request) {
    try {
        // 현재 로그인한 사용자 정보 조회
        $user = auth()->user() ?? \Illuminate\Support\Facades\Auth::user();
        
        // 세션에서 직접 사용자 ID 가져오기
        if (!$user && session('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')) {
            $userId = session('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
            $user = \App\Models\User::find($userId);
        }
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $projectId = $request->input('project_id');
        
        $repository = new PermissionsRepository();
        $permissions = $repository->getCurrentUserPermissions($user, $projectId);
        
        return response()->json($permissions);
        
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});