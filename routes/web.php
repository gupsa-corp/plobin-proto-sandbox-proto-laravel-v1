<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('700-page-dashboard/000-index');
})->name('dashboard');

// PMS Test Route - 실제 작동 확인용
Route::get('/pms/test', function() {
    return view('pms-test');
});

// PMS 수정 완료 확인 페이지
Route::get('/pms/test-success', function() {
    return view('pms-test-success');
});

// PMS 디버그 페이지
Route::get('/pms/debug', function() {
    return view('pms-debug');
});

// PMS 최종 현황 확인
Route::get('/pms/final-test', function() {
    return view('pms-final-test');
});

// 캘린더 테스트 가이드
Route::get('/pms/calendar-test', function() {
    return view('calendar-test');
});

// RFX System Routes - Detail Page (비 Livewire)
Route::get('/rfx/ai-analysis/{id}', function ($id) {
    $service = new \App\Services\Rfx\AiAnalysis\GetRequestDetail\Service();
    $result = $service->execute($id);

    if (!$result['success']) {
        return redirect()->route('rfx.ai-analysis')->with('error', '분석 결과를 찾을 수 없습니다.');
    }

    return view('700-page-rfx-ai-analysis-detail.000-index', [
        'request' => $result['data']
    ]);
})->name('rfx.ai-analysis.detail');

// RFX Navigation Test
Route::get('/test-rfx-nav', function () {
    return view('test-rfx-navigation');
})->name('test.rfx.nav');

// RFX Forms Test (simulating rfx/forms path)
Route::get('/rfx/forms-test', function () {
    return view('test-rfx-forms');
})->name('test.rfx.forms');

Route::get('/test', function () {
    return 'Test route works!';
});

Route::get('/test-upload', function () {
    return view('test-upload');
});

// File Upload and Management Pages
Route::get('/file-upload', function () {
    return view('700-page-file-upload/000-index');
})->name('file-upload');

Route::get('/file-list', function () {
    return view('700-page-file-list/000-index');
})->name('file-list');

// API routes for File Upload and Management
Route::post('/api/file-upload/create', \App\Http\Controllers\FileUpload\Create\Controller::class)->name('api.file-upload.create');
Route::get('/api/file-upload/list', \App\Http\Controllers\FileUpload\List\Controller::class)->name('api.file-upload.list');
Route::delete('/api/file-upload/delete', \App\Http\Controllers\FileUpload\Delete\Controller::class)->name('api.file-upload.delete');

// Document Analysis API
Route::post('/api/document-analysis/create', \App\Http\Controllers\DocumentAnalysis\Create\Controller::class)->name('api.document-analysis.create');

// API test route
Route::get('/api/test', function () {
    return response()->json(['message' => 'API test works']);
});