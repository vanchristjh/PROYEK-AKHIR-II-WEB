# Docker Deployment for SMAN1-Girsip Laravel Project

This guide provides instructions for deploying the SMAN1-Girsip Laravel project using Docker on your Virtual Machine.

## Prerequisites

- A Linux-based VM with SSH access
- Docker and Docker Compose installed on the VM
- Git installed on the VM

## Deployment Steps

1. **Clone the Repository**

   ```bash
   git clone [your-repo-url] /path/to/project
   cd /path/to/project
   ```

2. **Environment Configuration**

   Copy the `.env.example` file to `.env` and update the database configuration:

   ```bash
   cp .env.example .env
   ```

   Edit the `.env` file to match your Docker Compose configuration:
   
   ```
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=smansa-girsip
   DB_USERNAME=root
   DB_PASSWORD=your_secure_password
   ```

3. **Deploy with Docker**

   Run the deployment script:

   ```bash
   chmod +x docker-deploy.sh
   ./docker-deploy.sh
   ```

4. **Access the Application**

   The application will be available at `http://your-vm-ip:8000`

## Docker Commands Reference

- **Start the containers**: `docker-compose up -d`
- **Stop the containers**: `docker-compose down`
- **View container logs**: `docker-compose logs -f`
- **Restart containers**: `docker-compose restart`
- **Run Artisan commands**: `docker-compose exec app php artisan [command]`

## Maintenance

- **Database migrations**: `docker-compose exec app php artisan migrate`
- **Clear cache**: `docker-compose exec app php artisan cache:clear`
- **Optimize application**: `docker-compose exec app php artisan optimize`

## Troubleshooting

- **Check container status**: `docker-compose ps`
- **View application logs**: `docker-compose exec app tail -f /var/log/apache2/error.log`
- **Access container shell**: `docker-compose exec app bash`
