<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeWithQueue extends Command
{
    protected $signature = 'serve
                            {--host=127.0.0.1 : The host address to serve the application on}
                            {--port=8000 : The port to serve the application on}';

    protected $description = 'Serve the application on the PHP development server with queue worker';

    public function handle(): int
    {
        $host = $this->option('host');
        $port = $this->option('port');

        // 포트 정리 (기존 프로세스 종료)
        $this->killProcessOnPort($port);

        $this->info("Starting Laravel development server with queue worker...");
        $this->info("Server: http://{$host}:{$port}");
        $this->info("Queue: Running on port {$port}");
        $this->newLine();

        // 서버 프로세스 - PHP built-in 서버를 직접 실행
        $serverProcess = new Process([
            PHP_BINARY,
            '-S',
            "{$host}:{$port}",
            '-t',
            'public',
            'public/index.php'
        ], base_path());

        // 큐 워커 프로세스 (APP_URL 환경변수 설정)
        $queueProcess = new Process([
            PHP_BINARY,
            'artisan',
            'queue:work',
            '--tries=3',
            '--timeout=300'
        ], base_path(), [
            'APP_URL' => "http://{$host}:{$port}"
        ]);

        // 프로세스 시작
        $serverProcess->start();
        $queueProcess->start();

        $this->info("✓ Server started on http://{$host}:{$port}");
        $this->info("✓ Queue worker started (APP_URL: http://{$host}:{$port})");
        $this->info("Press Ctrl+C to stop");
        $this->newLine();

        // 프로세스 출력 실시간 표시
        while ($serverProcess->isRunning() || $queueProcess->isRunning()) {
            // 서버 출력 (stdout)
            $serverOutput = $serverProcess->getIncrementalOutput();
            if ($serverOutput) {
                $this->line("<fg=cyan>[SERVER]</> {$serverOutput}");
            }

            // 서버 에러 출력 (stderr - PHP built-in 서버는 여기로 출력)
            $serverError = $serverProcess->getIncrementalErrorOutput();
            if ($serverError) {
                // PHP 개발 서버 시작 메시지 필터링
                if (str_contains($serverError, 'Development Server') && str_contains($serverError, 'started')) {
                    // 정상 시작 메시지는 INFO로 표시
                    $lines = explode("\n", trim($serverError));
                    foreach ($lines as $line) {
                        if (!empty($line)) {
                            $this->line("<fg=cyan>[SERVER]</> <fg=green>✓</> Server ready");
                            break; // 첫 줄만 표시
                        }
                    }
                } elseif (str_contains($serverError, 'Accepted') || str_contains($serverError, 'Closing')) {
                    // 요청 로그는 표시하지 않음 (너무 많아서)
                } else {
                    // 실제 에러만 빨간색으로 표시
                    $this->error("[SERVER ERROR] {$serverError}");
                }
            }

            // 큐 출력
            $queueOutput = $queueProcess->getIncrementalOutput();
            if ($queueOutput) {
                $this->line("<fg=yellow>[QUEUE]</> {$queueOutput}");
            }

            // 큐 에러 출력
            $queueError = $queueProcess->getIncrementalErrorOutput();
            if ($queueError) {
                $this->error("[QUEUE ERROR] {$queueError}");
            }

            usleep(100000); // 0.1초 대기
        }

        return Command::SUCCESS;
    }

    protected function killProcessOnPort(int $port): void
    {
        $this->info("Checking for existing processes on port {$port}...");

        // macOS/Linux 시스템에서 포트를 사용 중인 프로세스 찾기
        $findProcess = new Process(['lsof', '-ti', ":{$port}"]);
        $findProcess->run();

        $pids = array_filter(explode("\n", trim($findProcess->getOutput())));

        if (empty($pids)) {
            $this->line("✓ Port {$port} is available");
            return;
        }

        foreach ($pids as $pid) {
            $this->warn("Killing process {$pid} on port {$port}...");

            $killProcess = new Process(['kill', '-9', $pid]);
            $killProcess->run();

            if ($killProcess->isSuccessful()) {
                $this->info("✓ Process {$pid} terminated");
            } else {
                $this->error("✗ Failed to kill process {$pid}");
            }
        }

        // 프로세스 종료 대기
        usleep(500000); // 0.5초 대기
        $this->line("✓ Port {$port} cleared");
    }

    public function __destruct()
    {
        // 프로세스 정리는 자동으로 처리됨
    }
}
