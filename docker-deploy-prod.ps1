# Script deployment Docker untuk SMAN1-Girsip di lingkungan produksi dengan PowerShell

Write-Host "=== Deployment Produksi SMAN1-Girsip dengan Docker ===" -ForegroundColor Green

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
Write-Host "`n[1/7] Setup environment..." -ForegroundColor Green
if (-not (Test-Path .env)) {
    Copy-Item .env.docker -Destination .env
    Write-Host "File .env dibuat dari .env.docker" -ForegroundColor Yellow
    Write-Host "Silakan edit file .env dengan kredensial yang sesuai" -ForegroundColor Yellow
    
    # Prompt untuk edit .env
    $response = Read-Host "Apakah Anda ingin mengedit .env sekarang? (y/n)"
    if ($response -eq "y") {
        notepad .env
    }
} else {
    Write-Host "File .env sudah ada, lewati pembuatan file" -ForegroundColor Yellow
}

# Ubah untuk mode produksi
Write-Host "`n[2/7] Menyiapkan konfigurasi produksi..." -ForegroundColor Green
(Get-Content .env) -replace 'APP_ENV=local', 'APP_ENV=production' | Set-Content .env
(Get-Content .env) -replace 'APP_DEBUG=true', 'APP_DEBUG=false' | Set-Content .env

# Build container produksi
Write-Host "`n[3/7] Membangun container Docker untuk produksi..." -ForegroundColor Green
docker-compose -f docker-compose.prod.yml build

# Jalankan container
Write-Host "`n[4/7] Menjalankan container Docker..." -ForegroundColor Green
docker-compose -f docker-compose.prod.yml up -d

# Tunggu database siap
Write-Host "`n[5/7] Menunggu database siap..." -ForegroundColor Green
Write-Host "Menunggu MySQL siap..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Setup Laravel
Write-Host "`n[6/7] Setup aplikasi Laravel..." -ForegroundColor Green
Write-Host "Generating application key..." -ForegroundColor Yellow
docker-compose -f docker-compose.prod.yml exec -T app php artisan key:generate --no-interaction --force

Write-Host "Running database migrations..." -ForegroundColor Yellow
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

Write-Host "Creating storage link..." -ForegroundColor Yellow
docker-compose -f docker-compose.prod.yml exec -T app php artisan storage:link

Write-Host "Optimizing Laravel..." -ForegroundColor Yellow
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# Info selesai
Write-Host "`n[7/7] Setup Task Scheduler untuk Laravel..." -ForegroundColor Green
Write-Host "Untuk Windows, buat Scheduled Task yang menjalankan perintah berikut setiap menit:" -ForegroundColor Yellow
Write-Host "docker-compose -f docker-compose.prod.yml exec -T app php artisan schedule:run" -ForegroundColor Yellow

Write-Host "`n=== Deployment selesai! ===" -ForegroundColor Green
Write-Host "Aplikasi dapat diakses di: http://domain-anda" -ForegroundColor Green
Write-Host "Catatan: Pastikan untuk mengkonfigurasi domain di server Nginx Anda" -ForegroundColor Yellow
Write-Host "Jika menggunakan HTTPS, pastikan sertifikat SSL sudah dikonfigurasi di /docker/nginx/ssl/" -ForegroundColor Yellow

# Info untuk backup
Write-Host "`n=== Informasi Backup Database ===" -ForegroundColor Green
Write-Host "Jalankan perintah berikut untuk backup database:" -ForegroundColor Yellow
$backupCmd = "docker-compose -f docker-compose.prod.yml exec db sh -c 'exec mysqldump -u root -p`"%MYSQL_ROOT_PASSWORD%`" %MYSQL_DATABASE% > /docker-entrypoint-initdb.d/backup-$(Get-Date -Format 'yyyyMMdd').sql'"
Write-Host $backupCmd -ForegroundColor Yellow
