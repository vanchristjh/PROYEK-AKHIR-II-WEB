# Docker deployment script for Laravel project without Nginx
Write-Host "Starting Docker deployment for Laravel project..." -ForegroundColor Green

# Build and start the Docker containers
Write-Host "Building and starting Docker containers..." -ForegroundColor Cyan
docker-compose down -v
docker-compose up -d --build

# Wait for the database to be ready
Write-Host "Waiting for the database to be ready..." -ForegroundColor Cyan
Start-Sleep -Seconds 10

# Set up the Laravel application
Write-Host "Setting up Laravel application..." -ForegroundColor Cyan
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan migrate --force

# Display information about running containers
Write-Host "Displaying running container details..." -ForegroundColor Cyan
docker-compose ps

Write-Host "Deployment complete! Your Laravel application should now be accessible at http://localhost:8000" -ForegroundColor Green