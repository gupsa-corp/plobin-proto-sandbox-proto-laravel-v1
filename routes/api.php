<?php

use Illuminate\Support\Facades\Route;

// Simple test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// File Upload API Routes
Route::post('/files/upload', \App\Http\Controllers\FileUpload\Create\Controller::class);
Route::post('/file-upload/create', \App\Http\Controllers\FileUpload\Create\Controller::class);
Route::get('/file-upload/list', \App\Http\Controllers\FileUpload\List\Controller::class);

// Document Analysis API Routes
Route::post('/document-analysis/create', \App\Http\Controllers\DocumentAnalysis\Create\Controller::class);

// RFX (Request for Analysis) API Routes
Route::post('/rfx/upload', \App\Http\Controllers\Rfx\Upload\Controller::class);
Route::post('/rfx/file-upload', \App\Http\Controllers\Rfx\FileUpload\Controller::class);
Route::get('/rfx/file-download/{file_id}', \App\Http\Controllers\Rfx\FileDownload\Controller::class);

// PMS (Project Management System) API Routes
Route::get('/pms/dashboard', \App\Http\Controllers\Pms\Dashboard\Controller::class)->name('api.pms.dashboard');
Route::get('/pms/projects', \App\Http\Controllers\Pms\Projects\Controller::class)->name('api.pms.projects');
Route::get('/pms/kanban', \App\Http\Controllers\Pms\Kanban\Controller::class)->name('api.pms.kanban');