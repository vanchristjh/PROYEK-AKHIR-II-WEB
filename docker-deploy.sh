#!/bin/bash

# Stop and remove any existing containers
echo "Stopping and removing any existing containers..."
docker-compose down -v

# Build and start the Docker containers
echo "Building and starting Docker containers..."
docker-compose up -d --build

# Wait for the database to be ready
echo "Waiting for the database to be ready..."
sleep 15

# Configure Git to avoid "dubious ownership" errors
echo "Configuring Git in the container..."
docker-compose exec app git config --global --add safe.directory /var/www/html

# Handle composer dependencies properly
echo "Setting up Laravel dependencies..."
docker-compose exec app composer update --no-interaction

# Set up the Laravel application
echo "Setting up Laravel application..."
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan migrate --force

# Create storage link
echo "Creating storage link..."
docker-compose exec app php artisan storage:link

# Optimize the application
echo "Optimizing the application..."
docker-compose exec app php artisan optimize

# Display information about running containers
echo "Deployment complete! Your application should be accessible at http://your-vm-ip:8000"
docker-compose ps