#!/bin/bash
set -e

# Wait for MySQL to be ready
# echo "Waiting for MySQL to be ready..."
# until php -r "
# try {
#     new PDO('mysql:host=db;port=3306;dbname=jumriz_db', 'jumriz_user', 'secret');
#     echo 'MySQL is ready!';
# } catch (PDOException \$e) {
#     echo 'Waiting for MySQL...';
#     exit(1);
# }
# " 2>/dev/null; do
#     sleep 3
# done

# # Install dependencies if vendor directory doesn't exist
# if [ ! -d "/var/www/html/vendor" ]; then
#     echo "Installing composer dependencies..."
#     composer install --no-interaction --optimize-autoloader
# fi

# # Generate application key if not set
# if [ -z "$(php artisan key:status --show)" ]; then
#     echo "Generating application key..."
#     php artisan key:generate
# fi

# # Run migrations
# echo "Running database migrations..."
# php artisan migrate --force

# # Run database seeding
# echo "Running database seeding..."
# php artisan db:seed --force

# Start Vite in the background for HMR
if [ -f "package.json" ]; then
    echo "Starting Vite development server..."
    npm install
    npm run dev -- --host 0.0.0.0 &
fi

# Clear and cache configurations
php artisan config:clear
php artisan cache:clear

# Execute the main command
exec "$@"