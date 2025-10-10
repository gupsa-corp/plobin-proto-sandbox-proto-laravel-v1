<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DevServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:serve {--port=8001 : The port to serve the application on}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start development server with queue worker';

    private $processes = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $port = $this->option('port');
        
        $this->info("🚀 Starting Plobin Service development server...");
        
        // 로그 디렉토리 생성
        $logDir = storage_path('logs/dev');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Laravel 서버 시작
        $this->info("🌐 Starting Laravel server on port {$port}...");
        $serverProcess = new Process(['php', 'artisan', 'serve', '--host=127.0.0.1', "--port={$port}"]);
        $serverProcess->setTimeout(null);
        $serverProcess->start();
        $this->processes[] = $serverProcess;

        // 잠시 대기 (서버가 시작될 때까지)
        sleep(2);

        // 큐 워커 시작
        $this->info("⚡ Starting queue worker...");
        $queueProcess = new Process(['php', 'artisan', 'queue:work', '--daemon', '--tries=3', '--timeout=300', '--sleep=3']);
        $queueProcess->setTimeout(null);
        $queueProcess->start();
        $this->processes[] = $queueProcess;

        $this->newLine();
        $this->line("✅ <fg=green>Development server started successfully!</>");
        $this->line("📍 Server URL: <fg=cyan>http://127.0.0.1:{$port}</>");
        $this->line("📊 RFX System: <fg=cyan>http://127.0.0.1:{$port}/rfx/dashboard</>");
        $this->newLine();
        $this->line("📋 Process Information:");
        $this->line("   - Laravel Server PID: <fg=yellow>{$serverProcess->getPid()}</>");
        $this->line("   - Queue Worker PID: <fg=yellow>{$queueProcess->getPid()}</>");
        $this->newLine();
        $this->line("<fg=red>Press Ctrl+C to stop all services</>");
        $this->newLine();

        // 프로세스 모니터링
        $this->monitorProcesses();
    }

    private function monitorProcesses()
    {
        // SIGINT 핸들러 등록
        pcntl_signal(SIGINT, function() {
            $this->stopProcesses();
            exit(0);
        });

        while (true) {
            pcntl_signal_dispatch();
            
            foreach ($this->processes as $index => $process) {
                if (!$process->isRunning()) {
                    $this->error("❌ Process {$index} has stopped unexpectedly");
                    $this->error($process->getErrorOutput());
                    $this->stopProcesses();
                    return 1;
                }
            }

            // 서버 상태 출력 (10초마다)
            static $counter = 0;
            if ($counter % 10 === 0) {
                $this->line("🔄 Services running... (Ctrl+C to stop)");
            }
            $counter++;

            sleep(1);
        }
    }

    private function stopProcesses()
    {
        $this->newLine();
        $this->info("🛑 Stopping development server...");
        
        foreach ($this->processes as $index => $process) {
            if ($process->isRunning()) {
                $process->stop();
                $this->line("✅ Process {$index} stopped");
            }
        }
        
        $this->info("🧹 All services stopped successfully");
    }
}
