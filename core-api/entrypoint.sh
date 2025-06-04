#!/bin/sh

PORT=${LARAVEL_APP_PORT:-8080}
WS_PORT=${REVERB_PORT:-6001}
VENDOR_PATH="/var/www/html/vendor/autoload.php"

echo "Waiting for PostgreSQL at $DB_HOST:$DB_PORT..."
until timeout 1 bash -c "echo > /dev/tcp/$DB_HOST/$DB_PORT" 2>/dev/null; do
  echo "Waiting for database connection..."
  sleep 2
done
echo "PostgreSQL is up."

# Ensure Composer dependencies are installed after volume mount
if [ ! -f "$VENDOR_PATH" ]; then
  echo "Vendor folder missing — running composer install..."
  composer install --no-dev --prefer-dist --optimize-autoloader
fi

# If no APP_KEY is set, generate one
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "null" ]; then
  echo "No APP_KEY found — generating one..."
  php artisan key:generate
fi

# Publish Sanctum migration (only if not already published)
if ! find ./database/migrations -type f -name '*_create_personal_access_tokens_table.php' | grep -q .; then
  echo "Publishing Sanctum migrations..."
  php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
fi

echo "Running migrations..."
php artisan migrate --force

echo "Starting Reverb on port $WS_PORT..."
php artisan reverb:start --host=0.0.0.0 --port="$WS_PORT" --no-interaction --no-ansi \
  >> /var/www/html/storage/logs/reverb.log 2>&1 &
REVERB_PID=$!

echo "Starting Laravel on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
