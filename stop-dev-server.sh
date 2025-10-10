#!/bin/bash

# Plobin Service 개발 서버 중지 스크립트

echo "🛑 Plobin Service 개발 서버를 중지합니다..."

# PID 파일에서 프로세스 ID 읽기
if [ -f "storage/logs/dev/server.pid" ]; then
    SERVER_PID=$(cat storage/logs/dev/server.pid)
    if kill -0 $SERVER_PID 2>/dev/null; then
        kill $SERVER_PID
        echo "✅ Laravel 서버 (PID: $SERVER_PID) 중지됨"
    else
        echo "⚠️  Laravel 서버 프로세스를 찾을 수 없음"
    fi
    rm -f storage/logs/dev/server.pid
fi

if [ -f "storage/logs/dev/queue.pid" ]; then
    QUEUE_PID=$(cat storage/logs/dev/queue.pid)
    if kill -0 $QUEUE_PID 2>/dev/null; then
        kill $QUEUE_PID
        echo "✅ 큐 워커 (PID: $QUEUE_PID) 중지됨"
    else
        echo "⚠️  큐 워커 프로세스를 찾을 수 없음"
    fi
    rm -f storage/logs/dev/queue.pid
fi

# 혹시 남은 프로세스들 정리
pkill -f "php artisan serve" 2>/dev/null
pkill -f "php artisan queue:work" 2>/dev/null

echo "🧹 모든 관련 프로세스가 정리되었습니다."
echo "📊 현재 실행 중인 PHP 프로세스:"
ps aux | grep "php artisan" | grep -v grep || echo "   없음"