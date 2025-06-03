# Laravel Project without Nginx Docker Deployment

This guide provides instructions for deploying your Laravel project on a VM using Docker without Nginx.

## Prerequisites

- Access to your VM with Docker and Docker Compose installed
- Git installed on your VM

## Deployment Steps

1. **Clone the Repository on Your VM**

   ```bash
   git clone <your-repository-url> /path/to/project
   cd /path/to/project
   ```

2. **Configure Environment Settings**

   Make sure your `.env` file has the correct database settings:

   ```
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=smansa-girsip
   DB_USERNAME=root
   DB_PASSWORD=secure_password_here
   ```

   Make sure to use the same password as specified in your `docker-compose.yml` file.

3. **Run the Deployment Script**

   Make the script executable and run it:

   ```bash
   chmod +x docker-deploy.sh
   ./docker-deploy.sh
   ```

## How It Works

This setup uses:
- PHP with Apache to serve your Laravel application
- MySQL as the database
- No Nginx, as requested

The application will be accessible at http://your-vm-ip:8000.

## Troubleshooting

If you encounter issues, try the following:

1. **Check container logs**:
   ```bash
   docker-compose logs app
   docker-compose logs db
   ```

2. **Access the container shell**:
   ```bash
   docker-compose exec app bash
   ```

3. **Restart containers**:
   ```bash
   docker-compose restart
   ```

4. **Rebuild from scratch**:
   ```bash
   docker-compose down -v
   docker-compose up -d --build
   ```

## Additional Commands

- **Stop all containers**: `docker-compose down`
- **View running containers**: `docker-compose ps`
- **Execute artisan commands**: `docker-compose exec app php artisan <command>`