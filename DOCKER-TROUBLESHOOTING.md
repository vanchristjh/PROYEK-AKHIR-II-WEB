# Laravel Docker Deployment Troubleshooting Guide

This guide addresses common issues encountered when deploying this Laravel application with Docker.

## Error: "Class Doctrine\DBAL\Driver\AbstractMySQLDriver not found"

This error occurs during migrations because the Doctrine DBAL package is missing.

### Solution:

1. Install the Doctrine DBAL package:

```bash
docker-compose exec app composer require doctrine/dbal
```

2. Try running migrations again:

```bash
docker-compose exec app php artisan migrate --force
```

## Error: "Unable to prepare route for serialization. Another route has already been assigned name"

This occurs when you have duplicate route names in your application, specifically with "guru.assignments.update".

### Solution:

1. Clear route cache:

```bash
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan optimize:clear
```

2. Run the route conflict diagnostic script:

```bash
chmod +x fix_route_deploy.sh
./fix_route_deploy.sh
```

3. Temporarily disable route caching in your deployment by commenting out:
   ```
   # docker-compose exec app php artisan route:cache
   ```

4. Fix duplicate route names in your application:

   - Check `routes/web.php` and `routes/api.php` for duplicate route names
   - Look specifically for multiple routes named "guru.assignments.update"
   - Rename one of the conflicting routes

## Deploying with Fixed Configuration

Once you've fixed these issues, run the deployment with:

```bash
chmod +x docker-deploy.sh
./docker-deploy.sh
```

## Complete Reset (If Needed)

If you need to completely reset your deployment:

```bash
# Stop and remove all containers, networks, and volumes
docker-compose down -v

# Rebuild from scratch
docker-compose build --no-cache
docker-compose up -d

# Run setup commands
docker-compose exec app composer require doctrine/dbal
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan migrate --force
```

## Checking Container Logs

If you still encounter issues, check container logs:

```bash
# For application logs
docker-compose logs app

# For database logs
docker-compose logs db

# For Apache error logs
docker-compose exec app cat /var/log/apache2/error.log
```
