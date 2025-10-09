<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('700-page-dashboard/000-index');
})->name('dashboard');

// PMS System Routes
Route::get('/pms/dashboard', \App\Livewire\Pms\Dashboard\Livewire::class)->name('pms.dashboard');
Route::get('/pms/projects', \App\Livewire\Pms\ProjectList\Livewire::class)->name('pms.projects');
Route::get('/pms/table-view', \App\Livewire\Pms\TableView\Livewire::class)->name('pms.table-view');
Route::get('/pms/kanban', \App\Livewire\Pms\KanbanBoard\Livewire::class)->name('pms.kanban');
Route::get('/pms/gantt', \App\Livewire\Pms\GanttChart\Livewire::class)->name('pms.gantt');
Route::get('/pms/calendar', \App\Livewire\Pms\CalendarView\Livewire::class)->name('pms.calendar');
Route::get('/pms/permissions', \App\Livewire\Pms\UserPermissions\Livewire::class)->name('pms.permissions');
Route::get('/pms/api-docs', \App\Livewire\Pms\ApiDocumentation\Livewire::class)->name('pms.api-docs');
Route::get('/pms/bookmarks', \App\Livewire\Pms\BookmarkManager\Livewire::class)->name('pms.bookmarks');
Route::get('/pms/ticket/{ticketId?}', \App\Livewire\Pms\TicketDetail\Livewire::class)->name('pms.ticket');

// RFX System Routes
Route::get('/rfx/upload', \App\Livewire\Rfx\FileUpload\Livewire::class)->name('rfx.upload');
Route::get('/rfx/files', \App\Livewire\Rfx\FileList\Livewire::class)->name('rfx.files');
Route::get('/rfx/analysis', \App\Livewire\Rfx\DocumentAnalysis\Livewire::class)->name('rfx.analysis');
Route::get('/rfx/requests', \App\Livewire\Rfx\AnalysisRequests\Livewire::class)->name('rfx.requests');
Route::get('/rfx/forms', \App\Livewire\Rfx\FormExecution\Livewire::class)->name('rfx.forms');
Route::get('/rfx/dashboard', \App\Livewire\Rfx\Dashboard\Livewire::class)->name('rfx.dashboard');

Route::get('/test', function () {
    return 'Test route works!';
});

Route::get('/test-upload', function () {
    return view('test-upload');
});

Route::get('/test-livewire', \App\Livewire\TestComponent::class)->name('test.livewire');

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

