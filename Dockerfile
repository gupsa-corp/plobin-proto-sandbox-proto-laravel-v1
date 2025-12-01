FROM php:8.3-cli-alpine

# Install dependencies (CLI + Supervisor for background processes)
RUN apk add --no-cache \
    supervisor \
    nodejs \
    npm \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    sqlite-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
  && docker-php-ext-install -j$(nproc) \
       pdo pdo_sqlite mbstring gd zip opcache intl bcmath exif pcntl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Set environment variables for Laravel runtime
ENV APP_FORCE_HTTPS=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=error \
    SESSION_DRIVER=file \
    CACHE_DRIVER=file \
    QUEUE_CONNECTION=database \
    APP_PORT=10001

# Graceful shutdown signal handling
STOPSIGNAL SIGTERM

# Copy and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader

COPY package.json package-lock.json ./
RUN npm ci

# Copy application
COPY . .

# Build and optimize
RUN composer dump-autoload --optimize \
    && npm run build

# Set permissions for Laravel directories
RUN chown -R www-data:www-data storage bootstrap/cache resources/views \
    && chmod -R 775 storage bootstrap/cache resources/views \
    && if [ -d "database" ]; then chmod -R 775 database; fi

# Set permissions for sandbox SQLite files (있을 때만 처리)
RUN if [ -d "sandbox/container" ]; then \
        find sandbox/container -type f -name "*.sqlite*" -exec chmod 666 {} \; && \
        find sandbox/container -type d -path "*/200-Database" -exec chmod 777 {} \; && \
        find sandbox/container -type f -name "*.sqlite*" -exec chown www-data:www-data {} \; ; \
    fi

# Create supervisor directory
RUN mkdir -p /etc/supervisor/conf.d

# Laravel initialization script
RUN cat > /usr/local/bin/start.sh << 'STARTEOF' \
&& echo '#!/bin/sh' > /usr/local/bin/start.sh \
&& echo 'set -e' >> /usr/local/bin/start.sh \
&& echo 'echo "Starting Laravel initialization..."' >> /usr/local/bin/start.sh \
&& echo 'cd /var/www/html' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo '# Set permissions for sandbox SQLite files' >> /usr/local/bin/start.sh \
&& echo 'echo "Setting permissions for sandbox SQLite files..."' >> /usr/local/bin/start.sh \
&& echo 'if [ -d "sandbox/container" ]; then' >> /usr/local/bin/start.sh \
&& echo '    find sandbox/container -type f -name "*.sqlite*" -exec chmod 666 {} \\; 2>/dev/null || true' >> /usr/local/bin/start.sh \
&& echo '    find sandbox/container -type d -path "*/200-Database" -exec chmod 777 {} \\; 2>/dev/null || true' >> /usr/local/bin/start.sh \
&& echo '    find sandbox/container -type f -name "*.sqlite*" -exec chown www-data:www-data {} \\; 2>/dev/null || true' >> /usr/local/bin/start.sh \
&& echo 'fi' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo '# Run migrations first' >> /usr/local/bin/start.sh \
&& echo 'echo "Running database migrations on connection (sqlite)..."' >> /usr/local/bin/start.sh \
&& echo 'php artisan migrate --force || echo "Migration failed or no migrations to run"' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo '# Clear and cache config after migration' >> /usr/local/bin/start.sh \
&& echo 'php artisan config:clear' >> /usr/local/bin/start.sh \
&& echo 'php artisan cache:clear || true' >> /usr/local/bin/start.sh \
&& echo 'php artisan route:clear' >> /usr/local/bin/start.sh \
&& echo 'php artisan view:clear' >> /usr/local/bin/start.sh \
&& echo 'php artisan config:cache' >> /usr/local/bin/start.sh \
&& echo 'php artisan view:cache' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo '# Generate supervisor config with environment variables' >> /usr/local/bin/start.sh \
&& echo 'echo "Generating Supervisor configuration with APP_PORT="${APP_PORT:-10001}"..."' >> /usr/local/bin/start.sh \
&& echo 'cat > /etc/supervisor/conf.d/supervisord.conf << SUPERVISOR_EOF' >> /usr/local/bin/start.sh \
&& echo '[supervisord]' >> /usr/local/bin/start.sh \
&& echo 'nodaemon=true' >> /usr/local/bin/start.sh \
&& echo 'user=root' >> /usr/local/bin/start.sh \
&& echo 'loglevel=info' >> /usr/local/bin/start.sh \
&& echo 'pidfile=/var/run/supervisord.pid' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo '[program:php-server]' >> /usr/local/bin/start.sh \
&& echo 'command=php artisan serve --host=0.0.0.0 --port=${APP_PORT:-10001}' >> /usr/local/bin/start.sh \
&& echo 'directory=/var/www/html' >> /usr/local/bin/start.sh \
&& echo 'autostart=true' >> /usr/local/bin/start.sh \
&& echo 'autorestart=true' >> /usr/local/bin/start.sh \
&& echo 'user=www-data' >> /usr/local/bin/start.sh \
&& echo 'stdout_logfile=/dev/stdout' >> /usr/local/bin/start.sh \
&& echo 'stdout_logfile_maxbytes=0' >> /usr/local/bin/start.sh \
&& echo 'stderr_logfile=/dev/stderr' >> /usr/local/bin/start.sh \
&& echo 'stderr_logfile_maxbytes=0' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo '[program:laravel-scheduler]' >> /usr/local/bin/start.sh \
&& echo 'command=sh -c "while true; do php artisan schedule:run; sleep 60; done"' >> /usr/local/bin/start.sh \
&& echo 'directory=/var/www/html' >> /usr/local/bin/start.sh \
&& echo 'autostart=true' >> /usr/local/bin/start.sh \
&& echo 'autorestart=true' >> /usr/local/bin/start.sh \
&& echo 'user=www-data' >> /usr/local/bin/start.sh \
&& echo 'stdout_logfile=/dev/stdout' >> /usr/local/bin/start.sh \
&& echo 'stdout_logfile_maxbytes=0' >> /usr/local/bin/start.sh \
&& echo 'stderr_logfile=/dev/stderr' >> /usr/local/bin/start.sh \
&& echo 'stderr_logfile_maxbytes=0' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo '[program:laravel-queue]' >> /usr/local/bin/start.sh \
&& echo 'command=php artisan queue:work --sleep=3 --tries=3 --timeout=90 --max-jobs=1000 --max-time=3600' >> /usr/local/bin/start.sh \
&& echo 'directory=/var/www/html' >> /usr/local/bin/start.sh \
&& echo 'autostart=true' >> /usr/local/bin/start.sh \
&& echo 'autorestart=true' >> /usr/local/bin/start.sh \
&& echo 'user=www-data' >> /usr/local/bin/start.sh \
&& echo 'numprocs=2' >> /usr/local/bin/start.sh \
&& echo 'process_name=%(program_name)s_%(process_num)02d' >> /usr/local/bin/start.sh \
&& echo 'stopwaitsecs=120' >> /usr/local/bin/start.sh \
&& echo 'stopsignal=SIGTERM' >> /usr/local/bin/start.sh \
&& echo 'stdout_logfile=/dev/stdout' >> /usr/local/bin/start.sh \
&& echo 'stdout_logfile_maxbytes=0' >> /usr/local/bin/start.sh \
&& echo 'stderr_logfile=/dev/stderr' >> /usr/local/bin/start.sh \
&& echo 'stderr_logfile_maxbytes=0' >> /usr/local/bin/start.sh \
&& echo 'SUPERVISOR_EOF' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo 'echo "Laravel initialization completed."' >> /usr/local/bin/start.sh \
&& echo '' >> /usr/local/bin/start.sh \
&& echo '# Start Supervisor' >> /usr/local/bin/start.sh \
&& echo 'echo "Starting Supervisor with PHP server (port "${APP_PORT:-10001}"), scheduler, and queue worker..."' >> /usr/local/bin/start.sh \
&& echo 'exec supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /usr/local/bin/start.sh
STARTEOF

RUN chmod +x /usr/local/bin/start.sh

EXPOSE ${APP_PORT}

# Use curl for healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=5 \
  CMD curl -fsS http://localhost:${APP_PORT}/api/healthz >/dev/null || exit 1

CMD ["/usr/local/bin/start.sh"]