<?php

use Illuminate\Support\Facades\Route;

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

// RFX System Routes - AI Analysis
Route::get('/rfx/ai-analysis', \App\Livewire\Rfx\AiAnalysis\Livewire::class)->name('rfx.ai-analysis');
Route::get('/rfx/ai-analysis/{requestId}/assets', \App\Livewire\Rfx\DocumentAssets\Livewire::class)->name('rfx.ai-analysis.assets');

// RFX System Routes - Other Livewire Components
Route::get('/rfx/dashboard', \App\Livewire\Rfx\Dashboard\Livewire::class)->name('rfx.dashboard');
Route::get('/rfx/upload', \App\Livewire\Rfx\FileUpload\Livewire::class)->name('rfx.upload');
Route::get('/rfx/files', \App\Livewire\Rfx\FileList\Livewire::class)->name('rfx.files');
Route::get('/rfx/analysis', \App\Livewire\Rfx\DocumentAnalysis\Livewire::class)->name('rfx.analysis');
Route::get('/rfx/requests', \App\Livewire\Rfx\AnalysisRequests\Livewire::class)->name('rfx.requests');
Route::get('/rfx/forms', \App\Livewire\Rfx\FormExecution\Livewire::class)->name('rfx.forms');

// Test Livewire Components
Route::get('/simple-test', \App\Livewire\SimpleTest::class);
Route::get('/test-livewire', \App\Livewire\TestComponent::class)->name('test.livewire');
