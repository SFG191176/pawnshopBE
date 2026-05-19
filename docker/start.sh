#!/bin/sh
set -e

echo "🚀 Starting Cam Do Son Hoang Production Server..."

# Create log directory for supervisor
mkdir -p /var/log/supervisor

# Cache configuration for performance
echo "📦 Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

echo "✅ Ready! Starting Nginx + PHP-FPM..."

# Start supervisor (manages nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
