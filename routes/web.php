<?php

use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return 'Test route works!';
});

Route::get('/sandbox/{container}/{domain}/{page}', function ($container, $domain, $page) {
    return view('700-page-sandbox.000-index', [
        'container' => $container,
        'domain' => $domain,
        'page' => $page
    ]);
})->name('sandbox.page');
