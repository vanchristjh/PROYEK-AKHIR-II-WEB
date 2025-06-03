# SMAN1-Girsip - Sistem Arsip Digital

## Tentang Aplikasi

SMAN1-Girsip adalah sistem arsip digital untuk SMAN 1 yang dikembangkan dengan Laravel.

## Deployment dengan Docker

Aplikasi ini bisa di-deploy dengan menggunakan Docker, baik di lingkungan pengembangan maupun produksi.

### Persyaratan

- Docker dan Docker Compose
- Untuk lingkungan produksi: VPS atau Dedicated Server dengan OS Linux (Ubuntu/Debian disarankan)

### Deployment di Lingkungan Pengembangan

1. Clone repository
2. Pastikan Docker sudah terinstall
3. Jalankan script deployment:

```bash
# Linux/Mac
bash docker-deploy.sh

# Windows (PowerShell)
.\docker-deploy.ps1
```

Aplikasi akan dapat diakses di `http://localhost`

### Deployment di Lingkungan Produksi

1. Upload kode aplikasi ke server
2. Pastikan Docker dan Docker Compose sudah terinstall
3. Edit file `.env.docker` sesuai kebutuhan:
   - Ubah `APP_URL` ke domain produksi
   - Sesuaikan kredensial database
   - Atur konfigurasi email
4. Pastikan domain sudah mengarah ke server
5. Ubah konfigurasi Nginx:
   - Edit file `docker/nginx/conf.d/app.prod.conf`
   - Ganti `your-domain.com` dengan domain asli
6. Setup SSL (jika menggunakan HTTPS):
   - Letakkan sertifikat di folder `docker/nginx/ssl/`
   - Pastikan nama file sertifikat sesuai dengan yang ada di konfigurasi Nginx
7. Jalankan script deployment produksi:

```bash
# Linux/Mac
bash docker-deploy-prod.sh

# Windows (PowerShell)
.\docker-deploy-prod.ps1
```

### Struktur Container Docker

Aplikasi ini menggunakan beberapa container Docker:

- **app**: Menjalankan aplikasi Laravel dengan PHP-FPM
- **webserver**: Nginx sebagai web server
- **db**: MySQL untuk database
- **redis**: Redis untuk caching (hanya di lingkungan produksi)

### Maintenance dan Backup

#### Backup Database

```bash
# Linux/Mac
docker-compose -f docker-compose.prod.yml exec db sh -c 'exec mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE > /docker-entrypoint-initdb.d/backup-$(date +%Y%m%d).sql'

# Windows (PowerShell)
docker-compose -f docker-compose.prod.yml exec db sh -c 'exec mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE > /docker-entrypoint-initdb.d/backup-$(Get-Date -Format 'yyyyMMdd').sql'
```

#### Restart Services

```bash
# Restart specific service
docker-compose -f docker-compose.prod.yml restart app

# Restart all services
docker-compose -f docker-compose.prod.yml restart
```

#### View Logs

```bash
# View logs from specific service
docker-compose -f docker-compose.prod.yml logs app

# Follow logs in real-time
docker-compose -f docker-compose.prod.yml logs -f app
```

### Troubleshooting

1. **Masalah Koneksi Database**
   - Periksa file `.env` untuk kredensial database
   - Pastikan container MySQL berjalan: `docker-compose ps`

2. **Masalah Permission**
   - Pastikan folder storage dan bootstrap/cache memiliki permission yang benar
   - Jalankan: `docker-compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache`

3. **Error 500**
   - Periksa log aplikasi: `docker-compose logs app`
   - Periksa log Nginx: `docker-compose logs webserver`

4. **Masalah SSL**
   - Pastikan sertifikat SSL valid dan jalurnya benar di konfigurasi Nginx

## Informasi Tambahan

Untuk informasi lebih detail tentang deployment dengan Docker, lihat file `DOCKER_DEPLOYMENT_GUIDE.md`.
