<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require_once __DIR__ . '/../104-Repository/500-query-user-settings.php';

// 사용자 컬럼 설정 조회
Route::get('user-column-settings', function (Request $request) {
    try {
        $repository = new UserSettingsRepository();
        $screenType = $request->input('screen_type', 'table_view');
        
        $settings = $repository->getColumnSettings($screenType);
        
        return response()->json(['success' => true, 'data' => $settings]);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 사용자 컬럼 설정 저장
Route::put('user-column-settings', function (Request $request) {
    try {
        $repository = new UserSettingsRepository();
        
        $screenType = $request->input('screen_type', 'table_view');
        $columnName = $request->input('column_name');
        $isVisible = $request->input('is_visible', 1);
        
        $repository->saveColumnSetting($screenType, $columnName, $isVisible);
        
        return response()->json(['success' => true, 'message' => 'Settings saved successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});

// 사용자 컬럼 설정 일괄 저장
Route::post('user-column-settings', function (Request $request) {
    try {
        $repository = new UserSettingsRepository();
        
        $screenType = $request->input('screen_type', 'table_view');
        $columnSettings = $request->input('column_settings', []);
        
        $repository->saveBulkColumnSettings($screenType, $columnSettings);
        
        return response()->json(['success' => true, 'message' => 'All settings saved successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
});