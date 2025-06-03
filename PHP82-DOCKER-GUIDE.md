# Laravel Docker Deployment Guide (PHP 8.2)

This guide provides instructions for deploying your Laravel project with PHP 8.2 on a VM using Docker without Nginx.

## Issues Fixed in This Deployment

1. **PHP Version Upgrade**: Updated from PHP 8.1 to PHP 8.2 to match your project's dependencies
2. **Git "Dubious Ownership" Fix**: Added Git configuration to resolve repository ownership issues
3. **Composer Dependency Management**: Properly handling dependencies with `composer update`
4. **No Nginx**: Using Apache directly from the PHP image instead of a separate Nginx container

## Deployment Steps

### 1. Prerequisites

- Docker and Docker Compose installed on your VM
- Git installed on your VM

### 2. Quick Deployment

Run the fix script to handle everything automatically:

```bash
chmod +x fix-docker-setup.sh
./fix-docker-setup.sh
```

### 3. Manual Deployment (if needed)

If you prefer to run the steps manually:

```bash
# Stop existing containers
docker-compose down -v

# Build and start containers with PHP 8.2
docker-compose up -d --build

# Fix Git ownership issue
docker-compose exec app git config --global --add safe.directory /var/www/html

# Update composer dependencies
docker-compose exec app composer update --no-interaction

# Set up Laravel
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan optimize
```

## Accessing Your Application

Your Laravel application should be accessible at `http://your-vm-ip:8000`

## Troubleshooting

If you encounter issues:

1. **Check container logs**:
   ```bash
   docker-compose logs app
   docker-compose logs db
   ```

2. **Enter the container shell**:
   ```bash
   docker-compose exec app bash
   ```

3. **Check PHP version inside the container**:
   ```bash
   docker-compose exec app php -v
   ```

4. **View Laravel errors**:
   ```bash
   docker-compose exec app tail -f /var/log/apache2/error.log
   ```

5. **Start over**:
   ```bash
   docker-compose down -v
   ./fix-docker-setup.sh
   ```
