#!/bin/bash

# Build the Docker image and start containers
docker-compose up -d

# Wait for the services to start up
echo "Waiting for services to start up..."
sleep 10

# Run database migrations
docker-compose exec app php artisan migrate --force

# Generate application key if not set
docker-compose exec app php artisan key:generate --force

# Optimize the application
docker-compose exec app php artisan optimize

# Display information about the running containers
echo "Application is now running. Container details:"
docker-compose ps