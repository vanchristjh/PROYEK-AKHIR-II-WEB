# Script deployment Docker untuk SMAN1-Girsip dengan PowerShell

Write-Host "=== Deployment SMAN1-Girsip dengan Docker ===" -ForegroundColor Green

# Pastikan Docker dan Docker Compose terinstall
try {
    docker --version | Out-Null
    docker-compose --version | Out-Null
} catch {
    Write-Host "Error: Docker dan Docker Compose harus terinstall" -ForegroundColor Red
    Write-Host "Install Docker: https://docs.docker.com/engine/install/" -ForegroundColor Yellow
    Write-Host "Docker Desktop sudah termasuk Docker Compose" -ForegroundColor Yellow
    exit 1
}

# Setup environment
Write-Host "`n[1/5] Setup environment..." -ForegroundColor Green
if (-not (Test-Path .env)) {
    Copy-Item .env.docker -Destination .env
    Write-Host "File .env dibuat dari .env.docker" -ForegroundColor Yellow
    Write-Host "Silakan edit file .env dengan kredensial yang sesuai" -ForegroundColor Yellow
} else {
    Write-Host "File .env sudah ada, lewati pembuatan file" -ForegroundColor Yellow
}

# Build container
Write-Host "`n[2/5] Membangun container Docker..." -ForegroundColor Green
docker-compose build

# Jalankan container
Write-Host "`n[3/5] Menjalankan container Docker..." -ForegroundColor Green
docker-compose up -d

# Setup Laravel
Write-Host "`n[4/5] Setup aplikasi Laravel..." -ForegroundColor Green
Write-Host "Installing composer dependencies..." -ForegroundColor Yellow
docker-compose exec -T app composer install --optimize-autoloader --no-dev

Write-Host "Generating application key..." -ForegroundColor Yellow
docker-compose exec -T app php artisan key:generate --no-interaction

Write-Host "Running database migrations..." -ForegroundColor Yellow
docker-compose exec -T app php artisan migrate --force

Write-Host "Creating storage link..." -ForegroundColor Yellow
docker-compose exec -T app php artisan storage:link

Write-Host "Optimizing Laravel..." -ForegroundColor Yellow
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Set permissions
Write-Host "`n[5/5] Setting file permissions..." -ForegroundColor Green
docker-compose exec -T app chown -R www-data:www-data /var/www/storage
docker-compose exec -T app chmod -R 775 /var/www/storage

Write-Host "`n=== Deployment selesai! ===" -ForegroundColor Green
Write-Host "Aplikasi dapat diakses di: http://localhost" -ForegroundColor Green
Write-Host "Catatan: Untuk produksi, pastikan untuk mengubah domain di konfigurasi Nginx dan .env" -ForegroundColor Yellow
