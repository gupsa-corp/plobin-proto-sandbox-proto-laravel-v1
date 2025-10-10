#!/bin/bash

# Plobin Service 개발 서버 시작 스크립트
# Laravel 서버와 큐 워커를 동시에 실행합니다.

echo "🚀 Plobin Service 개발 서버를 시작합니다..."

# 이전 프로세스 정리
echo "📋 이전 프로세스를 정리 중..."
pkill -f "php artisan serve"
pkill -f "php artisan queue:work"

# 로그 디렉토리 생성
mkdir -p storage/logs/dev

echo "🌐 Laravel 개발 서버 시작 중... (포트 8001)"
# Laravel 서버를 백그라운드에서 실행
nohup php artisan serve --host=127.0.0.1 --port=8001 > storage/logs/dev/server.log 2>&1 &
SERVER_PID=$!

echo "⚡ 큐 워커 시작 중..."
# 큐 워커를 백그라운드에서 실행
nohup php artisan queue:work --daemon --tries=3 --timeout=300 --sleep=3 > storage/logs/dev/queue.log 2>&1 &
QUEUE_PID=$!

echo ""
echo "✅ 서버가 성공적으로 시작되었습니다!"
echo "📍 서버 주소: http://127.0.0.1:8001"
echo "📊 RFX 시스템: http://127.0.0.1:8001/rfx/dashboard"
echo ""
echo "📋 프로세스 정보:"
echo "   - Laravel 서버 PID: $SERVER_PID"
echo "   - 큐 워커 PID: $QUEUE_PID"
echo ""
echo "📂 로그 파일:"
echo "   - 서버 로그: storage/logs/dev/server.log"
echo "   - 큐 로그: storage/logs/dev/queue.log"
echo ""
echo "🛑 서버를 중지하려면 다음 명령어를 실행하세요:"
echo "   ./stop-dev-server.sh"
echo "   또는: pkill -f 'php artisan'"
echo ""

# PID를 파일에 저장
echo $SERVER_PID > storage/logs/dev/server.pid
echo $QUEUE_PID > storage/logs/dev/queue.pid

echo "🔄 실시간 로그를 보려면:"
echo "   tail -f storage/logs/dev/server.log"
echo "   tail -f storage/logs/dev/queue.log"
echo ""
echo "Press Ctrl+C to stop monitoring, but servers will continue running..."

# 실시간 로그 모니터링
trap 'echo ""; echo "⚠️  서버는 백그라운드에서 계속 실행됩니다."; echo "🛑 완전히 중지하려면: ./stop-dev-server.sh"; exit' INT

# 두 로그를 동시에 모니터링
tail -f storage/logs/dev/server.log storage/logs/dev/queue.log