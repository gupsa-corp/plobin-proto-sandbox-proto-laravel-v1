.PHONY: serve serve-bg stop dev-status help

# 기본 포트 설정
PORT ?= 8001

# 컬러 코드
CYAN = \033[0;36m
YELLOW = \033[0;33m
GREEN = \033[0;32m
RED = \033[0;31m
NC = \033[0m # No Color

help: ## 사용 가능한 명령어 표시
	@echo "$(CYAN)Plobin Service 개발 서버 명령어:$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(YELLOW)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""

serve: ## Laravel 서버와 큐 워커를 동시에 실행
	@echo "$(CYAN)🚀 Starting Plobin Service development server...$(NC)"
	@echo "$(GREEN)📍 Server URL: http://127.0.0.1:$(PORT)$(NC)"
	@echo "$(GREEN)📊 RFX System: http://127.0.0.1:$(PORT)/rfx/dashboard$(NC)"
	@echo "$(RED)Press Ctrl+C to stop all services$(NC)"
	@echo ""
	@./dev-server.sh

serve-artisan: ## Laravel Artisan 명령어로 서버 실행
	@echo "$(CYAN)🚀 Starting server with Artisan command...$(NC)"
	php artisan dev:serve --port=$(PORT)

serve-npm: ## npm concurrently로 서버 실행
	@echo "$(CYAN)🚀 Starting server with npm...$(NC)"
	npm run serve:verbose

serve-bg: ## 백그라운드에서 서버 실행
	@echo "$(CYAN)🚀 Starting server in background...$(NC)"
	@nohup php artisan serve --host=127.0.0.1 --port=$(PORT) > storage/logs/dev/server.log 2>&1 &
	@nohup php artisan queue:work --daemon --tries=3 --timeout=300 > storage/logs/dev/queue.log 2>&1 &
	@echo "$(GREEN)✅ Server started in background$(NC)"
	@echo "$(GREEN)📍 Server URL: http://127.0.0.1:$(PORT)$(NC)"
	@echo "$(YELLOW)📋 Check status: make dev-status$(NC)"

stop: ## 개발 서버 중지
	@echo "$(RED)🛑 Stopping development server...$(NC)"
	@./stop-dev-server.sh

dev-status: ## 현재 실행 중인 개발 서버 상태 확인
	@echo "$(CYAN)📊 Development Server Status:$(NC)"
	@echo ""
	@echo "$(YELLOW)📋 Running PHP processes:$(NC)"
	@ps aux | grep "php artisan" | grep -v grep || echo "  No PHP artisan processes running"
	@echo ""
	@echo "$(YELLOW)📂 Log files:$(NC)"
	@if [ -f "storage/logs/dev/server.log" ]; then \
		echo "  ✅ Server log: storage/logs/dev/server.log"; \
		echo "     Last 3 lines:"; \
		tail -n 3 storage/logs/dev/server.log | sed 's/^/     /'; \
	else \
		echo "  ❌ No server log found"; \
	fi
	@if [ -f "storage/logs/dev/queue.log" ]; then \
		echo "  ✅ Queue log: storage/logs/dev/queue.log"; \
		echo "     Last 3 lines:"; \
		tail -n 3 storage/logs/dev/queue.log | sed 's/^/     /'; \
	else \
		echo "  ❌ No queue log found"; \
	fi

logs: ## 실시간 로그 보기
	@echo "$(CYAN)📋 Real-time logs (Ctrl+C to stop):$(NC)"
	@if [ -f "storage/logs/dev/server.log" ] && [ -f "storage/logs/dev/queue.log" ]; then \
		tail -f storage/logs/dev/server.log storage/logs/dev/queue.log; \
	else \
		echo "$(RED)❌ Log files not found. Start the server first.$(NC)"; \
	fi

clean: ## 로그 파일 및 캐시 정리
	@echo "$(CYAN)🧹 Cleaning up...$(NC)"
	@rm -rf storage/logs/dev/
	@php artisan cache:clear
	@php artisan config:clear
	@php artisan view:clear
	@echo "$(GREEN)✅ Cleanup completed$(NC)"

install: ## 초기 설정 및 의존성 설치
	@echo "$(CYAN)📦 Installing dependencies...$(NC)"
	@composer install
	@npm install
	@php artisan key:generate
	@touch database/database.sqlite
	@php artisan migrate
	@echo "$(GREEN)✅ Installation completed$(NC)"

# 기본 타겟
default: help