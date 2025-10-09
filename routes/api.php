<?php

use Illuminate\Support\Facades\Route;

// Simple test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// File Upload API Routes
Route::post('/files/upload', \App\Http\Controllers\FileUpload\Create\Controller::class);

// PMS (Project Management System) API Routes
Route::get('/pms/dashboard', \App\Http\Controllers\Pms\Dashboard\Controller::class)->name('api.pms.dashboard');
Route::get('/pms/projects', \App\Http\Controllers\Pms\Projects\Controller::class)->name('api.pms.projects');
Route::get('/pms/kanban', \App\Http\Controllers\Pms\Kanban\Controller::class)->name('api.pms.kanban');