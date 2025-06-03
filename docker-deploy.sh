#!/bin/bash
# Script deployment Docker untuk SMAN1-Girsip

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Deployment SMAN1-Girsip dengan Docker ===${NC}"

# Pastikan Docker dan Docker Compose terinstall
if ! command -v docker &> /dev/null || ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Error: Docker dan Docker Compose harus terinstall${NC}"
    echo -e "${YELLOW}Install Docker: https://docs.docker.com/engine/install/${NC}"
    echo -e "${YELLOW}Install Docker Compose: https://docs.docker.com/compose/install/${NC}"
    exit 1
fi

# Setup environment
echo -e "\n${GREEN}[1/5] Setup environment...${NC}"
if [ ! -f .env ]; then
    cp .env.docker .env
    echo -e "${YELLOW}File .env dibuat dari .env.docker${NC}"
    echo -e "${YELLOW}Silakan edit file .env dengan kredensial yang sesuai${NC}"
else
    echo -e "${YELLOW}File .env sudah ada, lewati pembuatan file${NC}"
fi

# Build container
echo -e "\n${GREEN}[2/5] Membangun container Docker...${NC}"
docker-compose build

# Jalankan container
echo -e "\n${GREEN}[3/5] Menjalankan container Docker...${NC}"
docker-compose up -d

# Setup Laravel
echo -e "\n${GREEN}[4/5] Setup aplikasi Laravel...${NC}"
echo -e "${YELLOW}Installing composer dependencies...${NC}"
docker-compose exec -T app composer install --optimize-autoloader --no-dev

echo -e "${YELLOW}Generating application key...${NC}"
docker-compose exec -T app php artisan key:generate --no-interaction

echo -e "${YELLOW}Running database migrations...${NC}"
docker-compose exec -T app php artisan migrate --force

echo -e "${YELLOW}Creating storage link...${NC}"
docker-compose exec -T app php artisan storage:link

echo -e "${YELLOW}Optimizing Laravel...${NC}"
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Set permissions
echo -e "\n${GREEN}[5/5] Setting file permissions...${NC}"
docker-compose exec -T app chown -R www-data:www-data /var/www/storage
docker-compose exec -T app chmod -R 775 /var/www/storage

echo -e "\n${GREEN}=== Deployment selesai! ===${NC}"
echo -e "${GREEN}Aplikasi dapat diakses di: http://localhost${NC}"
echo -e "${YELLOW}Catatan: Untuk produksi, pastikan untuk mengubah domain di konfigurasi Nginx dan .env${NC}"
