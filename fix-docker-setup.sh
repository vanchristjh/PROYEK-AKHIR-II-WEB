#!/bin/bash

# Exit if any command fails
set -e

echo "======================================"
echo "Fixing Laravel Docker Setup (No Nginx)"
echo "======================================"

echo "1. Stopping any running containers..."
docker-compose down -v

echo "2. Checking for webserver references in docker-compose.yml..."
if grep -q "webserver" docker-compose.yml; then
  echo "Found Nginx webserver reference in docker-compose.yml, removing it..."
  # Create backup
  cp docker-compose.yml docker-compose.yml.bak

  # Create new docker-compose file without webserver
  cat > docker-compose.yml << 'EOF'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: app
    restart: unless-stopped
    ports:
      - "8000:80"
    environment:
      - APP_ENV=production
      - DB_HOST=db
      - DB_PORT=3306
      - DB_DATABASE=smansa-girsip
      - DB_USERNAME=root
      - DB_PASSWORD=secure_password_here
    volumes:
      - ./:/var/www/html
      - ./storage:/var/www/html/storage
    networks:
      - app-network
    depends_on:
      - db

  db:
    image: mysql:8.0
    container_name: db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: smansa-girsip
      MYSQL_ROOT_PASSWORD: secure_password_here
      SERVICE_TAGS: dev
      SERVICE_NAME: mysql
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - app-network
    ports:
      - "3306:3306"

networks:
  app-network:
    driver: bridge

volumes:
  dbdata:
    driver: local
EOF
  echo "Created new docker-compose.yml without webserver/nginx"
fi

echo "3. Checking Dockerfile..."
if ! grep -q "apache" Dockerfile; then
  echo "Dockerfile doesn't use Apache, creating new one..."
  # Create backup
  cp Dockerfile Dockerfile.bak
  
  # Create new Dockerfile
  cat > Dockerfile << 'EOF'
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable mod_rewrite
RUN a2enmod rewrite

# Ensure the apache directory exists
RUN mkdir -p /var/www/html/docker/apache

# Create Apache configuration
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks MultiViews\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache service
CMD ["apache2-foreground"]
EOF
  echo "Created new Dockerfile with Apache"
fi

echo "4. Creating apache directory structure if needed..."
mkdir -p docker/apache

echo "5. Creating Apache configuration..."
mkdir -p docker/apache
cat > docker/apache/000-default.conf << 'EOF'
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

echo "6. Rebuilding and starting containers..."
docker-compose up -d --build

echo "7. Waiting for services to start..."
sleep 15

echo "8. Configuring Git in the container..."
docker-compose exec -T app git config --global --add safe.directory /var/www/html

echo "9. Setting up Laravel dependencies..."
docker-compose exec -T app composer update --no-interaction

echo "10. Setting up Laravel application..."
docker-compose exec -T app php artisan key:generate --force
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan migrate --force

echo "11. Creating storage link..."
docker-compose exec -T app php artisan storage:link

echo "12. Done! Your application should now be accessible at http://$(hostname -I | awk '{print $1}'):8000"
docker-compose ps
