#!/bin/bash

# Laravel Queue Worker 자동 시작 스크립트
# 사용법: ./start-queue-worker.sh

cd "$(dirname "$0")"

# 기존 워커 종료
pkill -f "queue:work"

# 큐 워커 백그라운드 실행
php artisan queue:work --daemon --tries=3 --timeout=300 > storage/logs/queue.log 2>&1 &

echo "Queue worker started with PID: $!"
echo "Log file: storage/logs/queue.log"
