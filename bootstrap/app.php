<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/livewire.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 모든 프록시 신뢰 (리버스 프록시 환경에서 HTTPS 헤더 전달)
        $middleware->trustProxies(at: '*');

        $middleware->api(remove: [
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // iframe 외부 연동을 위한 CSRF 예외 처리
        $middleware->validateCsrfTokens(except: [
            'rfx/*',
            'livewire/*',
            'livewire/update',
            'livewire/upload-file',
            'browser-mode/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
