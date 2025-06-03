# SMAN1-Girsip - Docker Deployment Guide

Panduan ini akan membantu Anda untuk melakukan setup dan deployment aplikasi SMAN1-Girsip menggunakan Docker di VPS atau hosting.

## Persyaratan

- Docker dan Docker Compose sudah terinstall di server
- Git (opsional, untuk clone repository)

## Langkah-langkah Deployment

### 1. Clone atau Upload Project ke Server

```bash
# Jika menggunakan git
git clone [URL_REPOSITORY] /path/to/project
# Atau upload project langsung ke server
```

### 2. Konfigurasi Environment

Salin file `.env.docker` menjadi `.env`:

```bash
cd /path/to/project
cp .env.docker .env
```

Edit file `.env` sesuai kebutuhan:
- Ubah `APP_URL` ke domain atau IP server Anda
- Sesuaikan `DB_PASSWORD` dan kredensial lainnya
- Atur konfigurasi email jika diperlukan

### 3. Build dan Jalankan Container Docker

```bash
cd /path/to/project
docker-compose build
docker-compose up -d
```

### 4. Setup Aplikasi Laravel

Masuk ke container aplikasi untuk setup Laravel:

```bash
docker-compose exec app bash

# Di dalam container, jalankan:
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Atur Permission

```bash
docker-compose exec app bash

# Di dalam container:
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage
```

### 6. Akses Aplikasi

Aplikasi sekarang dapat diakses melalui:
- http://domain-anda.com atau http://ip-server-anda

## Konfigurasi Tambahan

### SSL/HTTPS

Untuk menggunakan HTTPS:
1. Tambahkan sertifikat SSL ke `/docker/nginx/ssl/`
2. Edit konfigurasi Nginx di `/docker/nginx/conf.d/app.conf`

### Load Balancing (Opsional)

Untuk aplikasi dengan traffic tinggi, Anda dapat menambahkan lebih banyak container aplikasi dan menggunakan Nginx sebagai load balancer.

### Database Backup

Untuk backup database:

```bash
docker-compose exec db sh -c 'exec mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" smansa-girsip > /docker-entrypoint-initdb.d/backup.sql'
```

## Troubleshooting

### Error Database Connection
- Pastikan container database berjalan: `docker-compose ps`
- Cek kredensial database di .env

### Error Permission
- Pastikan folder storage dan bootstrap/cache memiliki permission yang benar

### Error 500
- Cek log: `docker-compose logs app`
- Cek log Nginx: `docker-compose logs webserver`
